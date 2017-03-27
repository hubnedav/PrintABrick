<?php

namespace AppBundle\Loader;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Keyword;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Part;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Service\LDrawService;
use AppBundle\Service\LDViewService;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
//use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;

//TODO refactor
class LDrawLoader extends Loader
{
    /**
     * @var Filesystem
     */
    private $ldraw;

    /**
     * @var string download URL with current LDraw library
     */
    private $ldraw_url;

    /**
     * @var LDViewService
     */
    private $LDViewService;

    /** @var LDrawService */
    private $ldrawService;

    /**
     * @param array $ldraw_url
     */
    public function setArguments(LDViewService $LDViewService, $ldraw_url, LDrawService $ldrawService)
    {
        $this->LDViewService = $LDViewService;
        $this->ldraw_url = $ldraw_url;
        $this->ldrawService = $ldrawService;
    }

    /**
     * Download current LDraw library and extract it to system tmp directory.
     *
     * @return string Absolute path to temporary Ldraw library
     */
    public function downloadLibrary()
    {
        $temp = $this->downloadFile($this->ldraw_url);

        // Create unique temporary directory
        $temp_dir = tempnam(sys_get_temp_dir(), 'printabrick.');
        mkdir($temp_dir);

        // Unzip downloaded library zip file to temporary directory
        $zip = new \ZipArchive();
        if ($zip->open($temp) != 'true') {
            throw new LogicException('Error :- Unable to open the Zip File');
        }
        $zip->extractTo($temp_dir);
        $zip->close();

        // Unlink downloaded zip file
        unlink($temp);

        return $temp_dir;
    }

    /**
     * @param $LDrawLibrary
     */
    public function loadData($LDrawLibrary)
    {
        $adapter = new Local($LDrawLibrary);
        $this->ldraw = new Filesystem($adapter);

        $this->loadParts();
    }

    /**
     * Load stl model by calling LDViewSevice and create new Model.
     *
     * @param $file
     * @param $header
     *
     * @throws \Exception
     *
     * @return Model|null
     */
    private function loadModel($file, $header)
    {
        if (($model = $this->em->getRepository(Model::class)->find($header['id'])) == null) {
            $model = new Model();
            $model->setNumber($header['id']);
        }

        $model->setAuthor($header['author']);
        $model->setModified($header['modified']);

        try {
            $stlFile = $this->LDViewService->datToStl($file, $this->ldraw)->getPath();
            $model->setFile($stlFile);
        } catch (\Exception $e) {
            throw $e; //TODO
        }

        return $model;
    }

    // TODO refactor
    public function loadParts()
    {
        $partManager = $this->ldrawService->getPartManager();
        $relationManager = $this->ldrawService->getPartRelationManager();

        $files = $this->ldraw->get('parts')->getContents();

        $this->initProgressBar(count($files));

        foreach ($files as $file) {
            if ($file['type'] == 'file' && $file['extension'] == 'dat') {
                $header = $this->getPartHeader($file);

                if ($this->isPartIncluded($header)) {
                    $part = $partManager->create($header['id']);

                    $part->setName($header['name']);
                    $part
                        ->setCategory($this->ldrawService->getCategoryManager()->createCategory($header['category']))
                        ->setType($this->ldrawService->getTypeManager()->create($header['type']));

                    if (isset($header['keywords'])) {
                        foreach ($header['keywords'] as $keyword) {
                            $keyword = stripslashes(strtolower(trim($keyword)));
                            $part->addKeyword($this->ldrawService->getKeywordManager()->createKeyword($keyword));
                        }
                    }

                    if (isset($header['subparts'])) {
                        if ($header['type'] == 'Alias') {
                            if (count($header['subparts']) == 1) {
                                $relationType = 'Alias';
                            } else {
                                $relationType = 'Subpart';
                            }
                        } else {
                            $relationType = 'Subpart';
                        }

                        foreach ($header['subparts'] as $referenceId) {
                            if ($referenceId != $this->getPrintedParentId($header['id'])) {
                                if ($this->getModel($referenceId) && $this->isPartIncluded($this->getPartHeader($this->getModel($referenceId)->getMetadata()))) {
                                    $referencedPart = $this->ldrawService->getPartManager()->create($referenceId);

                                    if ($relationType == 'Alias') {
                                        $parent = $referencedPart;
                                        $child = $part;
                                    } else {
                                        $parent = $part;
                                        $child = $referencedPart;
                                    }

                                    $partRelation = $relationManager->create($parent, $child, $relationType);
                                    $partRelation->setCount($partRelation->getCount() + 1);

                                    $relationManager->getRepository()->save($partRelation);
                                }
                            }
                        }
                    }

                    if (!in_array($header['type'], ['Alias'])) {
                        $part->setModel($this->loadModel($file, $header));
                    }

//                    try {
//                        $this->LDViewService->datToPng($file, $this->ldraw);
//                    } catch (\Exception $e) {
//                        dump($e->getMessage());
//                    }

                    $partManager->getRepository()->save($part);
                }
            }
            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     * Determine if part file should be loaded into database.
     *
     * @param $header
     *
     * @return bool
     */
    private function isPartIncluded($header)
    {
        // Do not include sticker parts and incomplete parts
        if (
            strpos($header['name'], 'Sticker') !== 0
            && strpos($header['id'], 's') !== 0 && $header['type'] != 'Subpart'
            && !$this->isStickerShortcutPart($header['id'])
            && !($this->isPrintedPart($header['id']) && $this->getModel($this->getPrintedParentId($header['id'])))
        ) {
            // If file is alias of another part determine if referenced file should be included
            if ($alias = $this->getObsoleteParentId($header['name'])) {
                if ($this->getModel($alias)) {
                    return $this->isPartIncluded($this->getPartHeader($this->getModel($alias)->getMetadata()));
                }

                return false;
            }

            return true;
        }

        return false;
    }

    private function isPrintedPart($id)
    {
        return preg_match('/(^.*)(p[0-9a-z][0-9a-z][0-9a-z]{0,1})$/', $id);
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
    private function getPrintedParentId($id)
    {
        if (preg_match('/(^.*)(p[0-9a-z][0-9a-z][0-9a-z]{0,1})$/', $id, $matches)) {
            return $matches[1];
        }

        return $id;
    }

    /**
     * Check if part is shortcut part of stricker and part.
     *
     * part name in format:
     *  nnnDnn, nnnnDnn, nnnnnDnn (a = alpha, n= numeric, x = alphanumeric)
     *
     *  http://www.ldraw.org/library/tracker/ref/numberfaq/
     *
     * @param $id
     *
     * @return string|null LDraw number of printed part parent
     */
    private function isStickerShortcutPart($id)
    {
        // some of files are in format nnnDaa
        return preg_match('/(^.*)(d[a-z0-9][a-z0-9])$/', $id);
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
    private function getObsoleteParentId($name)
    {
        if (preg_match('/^(~Moved to )(.*)$/', $name, $matches)) {
            return $matches[2];
        }

        return null;
    }

    private function getModel($id)
    {
        if ($this->ldraw->has('parts/'.$id.'.dat')) {
            return $this->ldraw->get('parts/'.$id.'.dat');
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
    private function getAlias($line)
    {
        if (preg_match('/^1(.*) (.*)\.(dat|DAT)$/', $line, $matches)) {
            return $matches[2];
        }

        return null;
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
    private function getPartHeader($file)
    {
        $header = [];

        $handle = $this->ldraw->readStream($file['path']);

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
                        $header['name'] = ltrim($line, '=_');

                        $firstLine = true;
                    }
                    // 0 !CATEGORY <CategoryName>
                    elseif (strpos($line, '!CATEGORY ') === 0) {
                        $header['category'] = trim(preg_replace('/^!CATEGORY /', '', $line));
                    }
                    // 0 !KEYWORDS <first keyword>, <second keyword>, ..., <last keyword>
                    elseif (strpos($line, '!KEYWORDS ') === 0) {
                        $header['keywords'] = explode(', ', preg_replace('/^!KEYWORDS /', '', $line));
                    }
                    // 0 Name: <Filename>.dat
                    elseif (strpos($line, 'Name: ') === 0) {
                        if (!isset($header['id'])) {
                            $header['id'] = preg_replace('/(^Name: )(.*)(.dat)/', '$2', $line);
                        }
                    }
                    // 0 Author: <Realname> [<Username>]
                    elseif (strpos($line, 'Author: ') === 0) {
                        $header['author'] = preg_replace('/^Author: /', '', $line);
                    }
                    // 0 !LDRAW_ORG Part|Subpart|Primitive|48_Primitive|Shortcut (optional qualifier(s)) ORIGINAL|UPDATE YYYY-RR
                    elseif (strpos($line, '!LDRAW_ORG ') === 0) {
                        $type = preg_replace('/(^!LDRAW_ORG )(.*)( UPDATE| ORIGINAL)(.*)/', '$2', $line);

                        $header['type'] = in_array($type, ['Part Alias', 'Shortcut Physical_Colour', 'Shortcut Alias', 'Part Physical_Colour']) ? 'Alias' : $type;

                        // Last modification date in format YYYY-RR
                        $date = preg_replace('/(^!LDRAW_ORG )(.*)( UPDATE | ORIGINAL )(.*)/', '$4', $line);
                        if (preg_match('/^[1-2][0-9]{3}-[0-9]{2}$/', $date)) {
                            $header['modified'] = \DateTime::createFromFormat('Y-m-d H:i:s', $date.'-01 00:00:00');
                        } else {
                            $header['modified'] = null;
                        }
                    }
                } elseif (strpos($line, '1 ') === 0) {
                    $header['subparts'][] = $this->getPrintedParentId($this->getAlias($line));
                }
            }

            if (strpos($header['name'], '~Moved to') === 0) {
                $header['type'] = 'Alias';
            } elseif (strpos($header['name'], '~') === 0) {
                $header['type'] = 'Obsolete/Subpart';
            }

            fclose($handle);

            return $header;
        }
        throw new LogicException('loadHeader error'); //TODO
    }
}
