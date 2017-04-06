<?php

namespace AppBundle\Utils;

use AppBundle\Exception\FileNotFoundException;
use League\Flysystem\File;
use Symfony\Component\Asset\Exception\LogicException;

class DatParser
{
    /** @var RelationMapper */
    protected $relationMapper;

    /**
     * DatParser constructor.
     *
     * @param RelationMapper $relationMapper
     */
    public function __construct($relationMapper)
    {
        $this->relationMapper = $relationMapper;
    }

    /**
     * Parse LDraw .dat file header identifying model store data to array.
     *
     * [
     *  'id' => string
     *  'name' => string
     *  'category' => string
     *  'keywords' => []
     *  'author' => string
     *  'modified' => DateTime
     *  'type' => string
     *  'subparts' => []
     * ]
     *
     * LDraw.org Standards: Official Library Header Specification (http://www.ldraw.org/article/398.html)
     *
     * @return array
     */
    public function parse($file)
    {
        $header = [];

        if(file_exists($file)) {
            try {
                $handle = fopen($file, 'r');

                if ($handle) {
                    $firstLine = false;

                    while (($line = fgets($handle)) !== false) {
                        $line = trim($line);

                        // Comments or META Commands
                        if (strpos($line, '0 ') === 0) {
                            $line = preg_replace('/^0 /', '', $line);

                            // 0 <CategoryName> <PartDescription>
                            if (!$firstLine) {
                                $array = explode(' ', ltrim(trim($line, 2), '=_~'));
                                $header['category'] = isset($array[0]) ? $array[0] : '';
                                $header['name'] = preg_replace('/ {2,}/', ' ', ltrim($line, '=_'));

                                $firstLine = true;
                            } // 0 !CATEGORY <CategoryName>
                            elseif (strpos($line, '!CATEGORY ') === 0) {
                                $header['category'] = trim(preg_replace('/^!CATEGORY /', '', $line));
                            } // 0 !KEYWORDS <first keyword>, <second keyword>, ..., <last keyword>
                            elseif (strpos($line, '!KEYWORDS ') === 0) {
                                $header['keywords'] = explode(', ', preg_replace('/^!KEYWORDS /', '', $line));
                            } // 0 Name: <Filename>.dat
                            elseif (strpos($line, 'Name: ') === 0) {
                                if (!isset($header['id'])) {
                                    $header['id'] = preg_replace('/(^Name: )(.*)(.dat)/', '$2', $line);
                                }
                            } // 0 Author: <Realname> [<Username>]
                            elseif (strpos($line, 'Author: ') === 0) {
                                $header['author'] = preg_replace('/^Author: /', '', $line);
                            } // 0 !LDRAW_ORG Part|Subpart|Primitive|48_Primitive|Shortcut (optional qualifier(s)) ORIGINAL|UPDATE YYYY-RR
                            elseif (strpos($line, '!LDRAW_ORG ') === 0) {
                                $type = preg_replace('/(^!LDRAW_ORG )(.*)( UPDATE| ORIGINAL)(.*)/', '$2', $line);

                                $header['type'] = $type;

                                // Last modification date in format YYYY-RR
                                $date = preg_replace('/(^!LDRAW_ORG )(.*)( UPDATE | ORIGINAL )(.*)/', '$4', $line);
                                if (preg_match('/^[1-2][0-9]{3}-[0-9]{2}$/', $date)) {
                                    $header['modified'] = \DateTime::createFromFormat('Y-m-d H:i:s', $date . '-01 00:00:00');
                                } else {
                                    $header['modified'] = null;
                                }
                            }
                        } elseif (strpos($line, '1 ') === 0) {
                            $header['subparts'][] = $this->getAlias($line);
                        }
                    }

                    if ($this->isStickerShortcutPart($header['name'], $header['id'])) {
                        $header['type'] = 'Sticker';
                    } elseif (($parent = $this->relationMapper->find($header['id'], 'alias_model')) != $header['id']) {
                        $header['type'] = 'Alias';
                        $header['subparts'] = null;
                        $header['parent'] = $parent;
                    } elseif (isset($header['subparts']) && count($header['subparts']) == 1 && in_array($header['type'], ['Part Alias', 'Shortcut Physical_Colour', 'Shortcut Alias', 'Part Physical_Colour'])) {
                        $header['parent'] = $header['subparts'][0];
                        $header['subparts'] = null;
                    } elseif ($parent = $this->getPrintedParentId($header['id'])) {
                        $header['type'] = 'Printed';
                        $header['subparts'] = null;
                        $header['parent'] = $parent;
                    } elseif ($parent = $this->getObsoleteParentId($header['name'])) {
                        $header['type'] = 'Alias';
                        $header['subparts'] = null;
                        $header['parent'] = $parent;
                    } elseif (strpos($header['name'], '~') === 0 && $header['type'] != 'Alias') {
                        $header['type'] = 'Obsolete/Subpart';
                    }

                    $header['name'] = ltrim($header['name'], '~');

                    fclose($handle);

                    return $header;
                }
            } catch (\Exception $exception) {
                dump($exception->getMessage());

                return null;
            }
        }
        return null;
    }

    /**
     * Get file reference from part line.
     *
     * Line type 1 is a sub-file reference. The generic format is:
     *  1 <colour> x y z a b c d e f g h i <file>
     *
     * LDraw.org Standards: File Format 1.0.2 (http://www.ldraw.org/article/218.html)
     *
     * @param $line
     *
     * @return string|null Filename of referenced part
     */
    public function getAlias($line)
    {
        if (preg_match('/^1(.*) (.*)\.(dat|DAT)$/', $line, $matches)) {
            return $matches[2];
        }

        return null;
    }

    /**
     * Get printed part parent id.
     *
     *  part name in format:
     *  nnnPxx, nnnnPxx, nnnnnPxx, nnnaPxx, nnnnaPxx (a = alpha, n= numeric, x = alphanumeric)
     *
     *  http://www.ldraw.org/library/tracker/ref/numberfaq/
     *
     * @param $id
     *
     * @return string|null LDraw number of printed part parent
     */
    public function getPrintedParentId($id)
    {
        if (preg_match('/(^.*)(p[0-9a-z][0-9a-z][0-9a-z]{0,1})$/', $id, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Check if part is shortcut part of stricker and part.
     *
     * part name in format:
     *  nnnDnn, nnnnDnn, nnnnnDnn (a = alpha, n= numeric, x = alphanumeric)
     *
     *  http://www.ldraw.org/library/tracker/ref/numberfaq/
     *
     * @param $name
     * @param $number
     *
     * @return string|null LDraw number of printed part parent
     */
    public function isStickerShortcutPart($name, $number)
    {
        if (strpos($name, 'Sticker') === 0) {
            return true;
        }

        // Check if in format nnnDaa == sticker
        return preg_match('/(^.*)(d[a-z0-9][a-z0-9])$/', $number);
    }

    /**
     * Get parent of obsolete part kept for reference.
     *
     *  part description in format:
     *  ~Moved to {new_number}
     *
     * http://www.ldraw.org/article/398.html  (Appendix II (02-Oct-06))
     *
     * @param $name
     *
     * @return string|null Filename of referenced part
     */
    public function getObsoleteParentId($name)
    {
        if (preg_match('/^(~Moved to )(.*)$/', $name, $matches)) {
            return $matches[2];
        }

        return null;
    }
}
