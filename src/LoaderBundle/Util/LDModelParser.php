<?php

namespace LoaderBundle\Util;

use LoaderBundle\Exception\ErrorParsingLineException;

class LDModelParser
{
    /**
     * Parse LDraw  model .dat file content and return associative array in format:
     * [
     *  'id' => string
     *  'name' => string
     *  'category' => string
     *  'keywords' => []
     *  'author' => string
     *  'modified' => DateTime
     *  'type' => string
     *  'subparts' => [
     *      'id' => [
     *         'color' => int
     *      ]
     *  ],
     *  'parent' => sting
     *  'licence' => string
     * ].
     *
     * LDraw.org Standards: Official Library Header Specification (http://www.ldraw.org/article/398.html)
     *
     * @param $string
     *
     * @throws ErrorParsingLineException
     *
     * @return array
     */
    public function parse($string)
    {
        $model = [
            'id' => null,
            'name' => null,
            'category' => null,
            'keywords' => [],
            'author' => null,
            'modified' => null,
            'type' => null,
            'subparts' => [],
            'parent' => null,
            'license' => null,
        ];

        $firstLine = false;
        foreach (explode("\n", $string) as $line) {
            $line = trim($line);

            // Comments or META Commands
            if (strpos($line, '0 ') === 0) {
                $line = preg_replace('/^0 /', '', $line);

                // 0 <CategoryName> <PartDescription>
                if (!$firstLine) {
                    $array = explode(' ', ltrim(trim($line, 2), '=_~'));
                    $model['category'] = isset($array[0]) ? $array[0] : '';
                    $model['name'] = preg_replace('/ {2,}/', ' ', ltrim($line, '_'));

                    $firstLine = true;
                }
                // 0 !CATEGORY <CategoryName>
                elseif (strpos($line, '!CATEGORY ') === 0) {
                    $model['category'] = trim(preg_replace('/^!CATEGORY /', '', $line));
                }
                // 0 !KEYWORDS <first keyword>, <second keyword>, ..., <last keyword>
                elseif (strpos($line, '!KEYWORDS ') === 0) {
                    $keywords = explode(',', preg_replace('/^!KEYWORDS /', '', $line));
                    foreach ($keywords as $keyword) {
                        $keyword = trim($keyword);
                        if ($keyword && !in_array($keyword, $model['keywords'])) {
                            $model['keywords'][] = $keyword;
                        }
                    }
                }
                // 0 Name: <Filename>.dat
                elseif (strpos($line, 'Name: ') === 0 && !isset($header['id'])) {
                    $model['id'] = preg_replace('/(^Name: )(.*)(.dat|.DAT)/', '$2', $line);
                }
                // 0 Author: <Realname> [<Username>]
                elseif (strpos($line, 'Author: ') === 0) {
                    $model['author'] = preg_replace('/^Author: /', '', $line);
                }
                // 0 !LDRAW_ORG Part|Subpart|Primitive|48_Primitive|Shortcut (optional qualifier(s)) ORIGINAL|UPDATE YYYY-RR
                elseif (strpos($line, '!LDRAW_ORG ') === 0) {
                    $type = preg_replace('/(^!LDRAW_ORG )(.*)( UPDATE| ORIGINAL)(.*)/', '$2', $line);

                    $model['type'] = $type;

                    // Last modification date in format YYYY-RR
                    $date = preg_replace('/(^!LDRAW_ORG )(.*)( UPDATE | ORIGINAL )(.*)/', '$4', $line);
                    if (preg_match('/^[1-2][0-9]{3}-[0-9]{2}$/', $date)) {
                        $model['modified'] = \DateTime::createFromFormat('Y-m-d H:i:s', $date.'-01 00:00:00');
                    }
                }
                // 0 !LICENSE Redistributable under CCAL version 2.0 : see CAreadme.txt | 0 !LICENSE Not redistributable : see NonCAreadme.txt
                elseif (strpos($line, '!LICENSE ') === 0) {
                    $model['license'] = preg_replace('/(^!LICENSE )(.*) : (.*)$/', '$2', $line);
                }
            } elseif (strpos($line, '1 ') === 0) {
                if ($reference = $this->getReferencedModelNumber($line)) {
                    $id = strtolower($reference['id']);
                    $color = strtolower($reference['color']);

                    // group subparts by color and id
                    if (isset($model['subparts'][$id][$color])) {
                        $model['subparts'][$id][$color] = $model['subparts'][$id][$color] + 1;
                    } else {
                        $model['subparts'][$id][$color] = 1;
                    }
                }
            } elseif (!empty($line) && !in_array($line[0], ['2', '3', '4', '5'])) {
                throw new ErrorParsingLineException($model['id'], $line);
            }
        }

        if ($this->isSticker($model['name'], $model['id']) && !in_array($model['type'], ['48_Primitive', '8_Primitive', 'Primitive', 'Subpart'])) {
            $model['type'] = 'Sticker';
        } elseif (count($model['subparts']) == 1 && in_array($model['type'], ['Part Alias', 'Shortcut Physical_Colour', 'Shortcut Alias', 'Part Physical_Colour'])) {
            $model['parent'] = array_keys($model['subparts'])[0];
        } elseif (($parent = $this->getPrintedModelParentNumber($model['id'])) && !in_array($model['type'], ['48_Primitive', '8_Primitive', 'Primitive', 'Subpart'])) {
            $model['type'] = 'Printed';
            $model['parent'] = $parent;
        } elseif ($parent = $this->getObsoleteModelParentNumber($model['name'])) {
            $model['parent'] = $parent;
        }

        return $model;
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
     * @return array|null Filename of referenced part
     */
    public function getReferencedModelNumber($line)
    {
        $line = strtolower(preg_replace('!\s+!', ' ', $line));

        // Do not load inverse part as subpart of model
        if (preg_match('/^1 ([0-9]{1,3}) (0 0 0 -1 0 0 0 1 0 0 0 1) (.*)\.dat$/', $line, $matches)) {
            return null;
        } elseif (preg_match('/^1 ([0-9]{1,3}) (.*) (.*)\.dat$/', $line, $matches)) {
            $id = str_replace('\\', DIRECTORY_SEPARATOR, $matches[3]);
            $color = $matches[1];

            return ['id' => $id, 'color' => $color];
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
    public function getPrintedModelParentNumber($id)
    {
        if (preg_match('/(^.*)(p[0-9a-z]{2,3})$/', $id, $matches)) {
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
     * @return bool
     */
    public function isSticker($name, $number)
    {
        if (strpos($name, 'Sticker') === 0) {
            return true;
        }

        // Check if in format n*Daa == sticker
        return preg_match('/(^.*)(d[0-9a-z]{2})$/', $number);
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
    public function getObsoleteModelParentNumber($name)
    {
        if (preg_match('/^(~Moved to )(.*)$/', $name, $matches)) {
            return str_replace('\\', DIRECTORY_SEPARATOR, $matches[2]);
        }

        return null;
    }
}
