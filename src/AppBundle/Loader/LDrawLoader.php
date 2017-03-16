<?php

namespace AppBundle\Loader;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Keyword;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Part;
use AppBundle\Entity\LDraw\Part_Relation;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Service\LDViewService;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
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

    /**
     * @param array $ldraw_url
     */
    public function setArguments(LDViewService $LDViewService, $ldraw_url)
    {
        $this->LDViewService = $LDViewService;
        $this->ldraw_url = $ldraw_url;
    }

    /**
     * Download current LDraw library and extract it to system tmp directory.
     *
     * @return string Absolute path to temporary Ldraw library
     */
    public function downloadLibrary()
    {
        $this->output->writeln('Downloading LDraw library form ldraw.org');
        $temp = $this->downloadFile($this->ldraw_url);
        $temp_dir = tempnam(sys_get_temp_dir(), 'printabrick.');
        if (file_exists($temp_dir)) {
            unlink($temp_dir);
        }
        mkdir($temp_dir);
        $zip = new \ZipArchive();
        if ($zip->open($temp) != 'true') {
            echo 'Error :- Unable to open the Zip File';
        }
        $zip->extractTo($temp_dir);
        $zip->close();
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
            $model->setId($header['id']);
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
        $files = $this->ldraw->get('parts')->getContents();

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $progressBar = new ProgressBar($this->output, count($files));
        $progressBar->setFormat('very_verbose');
        $progressBar->setMessage('Loading LDraw library models');
        $progressBar->setFormat('%message:6s% %current%/%max% [%bar%]%percent:3s%% (%elapsed:6s%/%estimated:-6s%)');
        $progressBar->start();

        foreach ($files as $file) {
            if ($file['type'] == 'file' && $file['extension'] == 'dat') {
                $header = $this->getPartHeader($file);

                if ($this->isPartIncluded($header)) {
                    if (null == ($part = $this->em->getRepository(Part::class)->find($header['id']))) {
                        $part = new Part();
                        $part->setId($header['id']);
                    }
                    $part->setName($header['name']);

                    if (($category = $this->em->getRepository(Category::class)->findOneBy(['name' => $header['category']])) == null) {
                        $category = new Category();
                        $category->setName($header['category']);
                    }
                    $part->setCategory($category);

                    if (($type = $this->em->getRepository(Type::class)->findOneBy(['name' => $header['type']])) == null) {
                        $type = new Type();
                        $type->setName($header['type']);
                    }
                    $part->setType($type);

                    if (isset($header['keywords'])) {
                        foreach ($header['keywords'] as $kword) {
                            $kword = trim($kword);
                            if (($keyword = $this->em->getRepository(Keyword::class)->findOneBy(['name' => $kword])) == null) {
                                $keyword = new Keyword();
                                $keyword->setName($kword);
                            }
                            $part->addKeyword($keyword);
                        }
                    }

                    if ($printParentId = $this->getPrinetedParentId($header['id'])) {
                        if (($printParent = $this->em->getRepository(Part::class)->find($printParentId)) == null) {
                            $printParent = new Part();
                            $printParent->setId($printParentId);

                            if (!$this->ldraw->has('parts/'.$printParentId.'.dat')) {
                                $printParent->setModel($this->loadModel($file, $header));
                            }
                        }

                        if (($alias = $this->em->getRepository(Part_Relation::class)->find(['parent' => $printParent, 'child' => $part, 'type' => 'Print'])) == null) {
                            $alias = new Part_Relation();
                            $alias->setParent($printParent);
                            $alias->setChild($part);
                            $alias->setCount(0);
                            $alias->setType('Print');
                        }
                        $this->em->persist($alias);
                    }

                    if (isset($header['subparts']) && $header['type'] != 'Print') {
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
                            if ($referenceId != $this->getPrinetedParentId($header['id'])) {
                                if ($this->ldraw->has('parts/'.$referenceId.'.dat') && $this->isPartIncluded($this->getPartHeader($this->ldraw->get('parts/'.$referenceId.'.dat')->getMetadata()))) {
                                    if (($referencedPart = $this->em->getRepository(Part::class)->find($referenceId)) == null) {
                                        $referencedPart = new Part();
                                        $referencedPart->setId($referenceId);

                                        $this->em->persist($referencedPart);
                                    }

                                    if($relationType == 'Alias') {
                                        $parent = $referencedPart;
                                        $child = $part;
                                    } else {
                                        $parent = $part;
                                        $child = $referencedPart;
                                    }

                                    if (($alias = $this->em->getRepository(Part_Relation::class)->find(['parent' => $parent, 'child' => $child, 'type' => $relationType])) == null) {
                                        $alias = new Part_Relation();
                                        $alias->setParent($parent);
                                        $alias->setChild($child);
                                        $alias->setCount(0);
                                        $alias->setType($relationType);
                                    }

                                    $alias->setCount($alias->getCount() + 1);

                                    $this->em->persist($alias);
                                }
                            }
                        }
                    }

                    if (!in_array($header['type'], ['Print', 'Alias'])) {
                        $part->setModel($this->loadModel($file, $header));
                    }

                    try {
                        $this->LDViewService->datToPng($file, $this->ldraw);
                    } catch (\Exception $e) {
                        dump($e->getMessage());
                    }

                    $this->em->persist($part);
                    $this->em->flush();
                    $this->em->clear();
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
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
        if (strpos($header['name'], 'Sticker') !== 0 && strpos($header['id'], 's') !== 0 && $header['type'] != 'Subpart' && !$this->isStickerShortcutPart($header['id'])) {
            // If file is alias of another part determine if referenced file should be included
            if (strpos($header['name'], '~Moved to ') === 0) {
                // Get file path of referenced part file
                $alias = 'parts/'.$this->getObsoleteParentId($header['name']).'.dat';
                if ($this->ldraw->has($alias)) {
                    return $this->isPartIncluded($this->getPartHeader($this->ldraw->get($alias)->getMetadata()));
                }

                return false;
            }

            return true;
        }

        return false;
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
    private function getPrinetedParentId($id)
    {
        if (preg_match('/(^.*)(p[0-9a-z][0-9a-z][0-9a-z]{0,1})$/', $id, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Check if part is shortcut part of stricker and part
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
                        $header['id'] = preg_replace('/(^Name: )(.*)(.dat)/', '$2', $line);
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
                    $header['subparts'][] = $this->getAlias($line);
                }
            }

            if (strpos($header['name'], '~Moved to') === 0) {
                $header['type'] = 'Alias';
            } elseif (strpos($header['name'], '~') === 0) {
                $header['type'] = 'Obsolete/Subpart';
            } elseif ($printParentId = $this->getPrinetedParentId($header['id'])) {
                $header['type'] = 'Print';
            }

            fclose($handle);

            return $header;
        }
        throw new LogicException('loadHeader error'); //TODO
    }
}
