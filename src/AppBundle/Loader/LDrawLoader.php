<?php

namespace AppBundle\Loader;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Keyword;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Part;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Service\LDViewService;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\Finder;

class LDrawLoader extends Loader
{
    /**
     * @var Filesystem
     */
    private $ldraw;

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

    public function loadModels($LDrawLibrary)
    {
        $adapter = new Local($LDrawLibrary);
        $this->ldraw = new Filesystem($adapter);
//        $files = $this->ldraw->get('parts')->getContents();

        $files = $this->ldraw->get('parts')->getContents();

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

//        $finder = new Finder();
//        $files = $finder->files()->name('*.dat')->depth('== 0')->in(getcwd().DIRECTORY_SEPARATOR.$LDrawLibrary.DIRECTORY_SEPARATOR.'parts');

        $progressBar = new ProgressBar($this->output, count($files));
        $progressBar->setFormat('very_verbose');
        $progressBar->setMessage('Loading LDraw library models');
        $progressBar->setFormat('%message:6s% %current%/%max% [%bar%]%percent:3s%% (%elapsed:6s%/%estimated:-6s%)');
        $progressBar->start();

        foreach ($files as $file) {
            if ($file['type'] == 'file' && $file['extension'] == 'dat') {
                $header = $this->getPartHeader($file);

                if ($this->fileFilter($header)) {
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

                    if ($header['print_of_id']) {
                        if (($printParent = $this->em->getRepository(Part::class)->find($header['print_of_id'])) == null) {
                            $printParent = new Part();
                            $printParent->setId($header['print_of_id']);
                        }
                        $part->setPrintOf($printParent);
                    }

                    if ($header['alias_of_id']) {
                        if (($aliasParent = $this->em->getRepository(Part::class)->find($header['alias_of_id'])) == null) {
                            $aliasParent = new Part();
                            $aliasParent->setId($header['alias_of_id']);
                        }
                        $part->setAliasOf($aliasParent);
                    }

                    if (!$header['print_of_id'] && !$header['alias_of_id']) {
                        if (($model = $this->em->getRepository(Model::class)->find($header['id'])) == null) {
                            $model = new Model();
                            $model->setId($header['id']);
                        }

                        $model->setAuthor($header['author']);
                        $model->setModified($header['modified']);

                        try {
                            $file = $this->LDViewService->datToStl($file, $this->ldraw)->getPath();
                        } catch (\Exception $e) {
                            dump($e);
                        }

                        $model->setFile($file);

                        $part->setModel($model);
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

    private function fileFilter($header)
    {
        if (strpos($header['name'], 'Sticker') !== 0 &&
            (strpos($header['name'], '~') !== 0 || strpos($header['name'], '~Moved to ') === 0) &&
            $header['type'] !== 'Subpart') {
            if (strpos($header['name'], '~Moved to ') === 0) {
                $filepath = str_replace('\\', DIRECTORY_SEPARATOR, 'parts/'.$header['alias_of_id'].'.dat');

                return $this->fileFilter($this->getPartHeader($this->ldraw->get($filepath)->getMetadata()));
            }

            return true;
        }

        return false;
    }

    private function getPrinetedParentId($filename)
    {
        // nnnPxx, nnnnPxx, nnnnnPxx, nnnaPxx, nnnnaPxx
        //  where (a = alpha, n= numeric, x = alphanumeric)

        if (preg_match('/(^.*)(p[0-9a-z][0-9a-z][0-9a-z]{0,1})$/', $filename, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function isShortcutPart($filename)
    {
        // nnnCnn, nnnnCnn, nnnnnCnn	        Shortcut assembly of part nnn, nnnn or nnnnn with other parts or formed version of flexible part nnn, nnnn or nnnnn.
        // nnnCnn-Fn, nnnnCnn-Fn, nnnnnCnn-Fn   Positional variant of shortcut assembly of movable parts, comprising part nnn, nnnn or nnnnn with other parts.
        //  where (a = alpha, n= numeric, x = alphanumeric)

        return preg_match('/(^.*)(c[0-9][0-9])(.*)/', $filename);
    }

    private function getAliasParentId($name)
    {
        if (preg_match('/^(~Moved to )(.*)$/', $name, $matches)) {
            return $matches[2];
        }

        return null;
    }

    private function getAlias($line)
    {
        //        1 <colour> x y z a b c d e f g h i <file>

        if (preg_match('/^1(.*) (.*)\.dat$/', $line, $matches)) {
            return $matches[2];
        }

        return null;
    }

    /**
     * @return array
     */
    private function getPartHeader($file)
    {
        $header = [];

//        $handle = fopen($file->getRealPath(), 'r');

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
                        $array = explode(' ', trim($line), 2);
                        $header['category'] = isset($array[0]) ? ltrim($array[0], '=_~') : '';
                        $header['name'] = $line;

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
                        $header['type'] = $type;

                        // Last modification date in format YYYY-RR
                        $date = preg_replace('/(^!LDRAW_ORG )(.*)( UPDATE | ORIGINAL )(.*)/', '$4', $line);
                        if (preg_match('/^[1-2][0-9]{3}-[0-9]{2}$/', $date)) {
                            $header['modified'] = \DateTime::createFromFormat('Y-m-d H:i:s', $date.'-01 00:00:00');
                        } else {
                            $header['modified'] = null;
                        }
                    }
                } elseif (strpos($line, '1 ') === 0) {
                    if ($header['type'] == 'Part Alias' || $header['type'] == 'Shortcut Physical_Colour' || $header['type'] == 'Shortcut Alias') {
                        // "=" -> Alias name for other part kept for referece - do not include model -> LINK
                        // "_" -> Physical_color  - do not include model -> LINK
                        $header['name'] = ltrim($header['name'], '=_');
                        $header['alias_of_id'] = $this->getAlias($line);
                    } elseif ($header['type'] == 'Shortcut') {
                        $header['subparts'][] = $this->getAlias($line);
                    }
                } elseif ($line != '') {
                    break;
                }
            }

            if (isset($header['id'])) {
                $header['print_of_id'] = $this->getPrinetedParentId($header['id']);
            }

            if (isset($header['name']) && !isset($header['alias_of_id'])) {
                $header['alias_of_id'] = $this->getAliasParentId($header['name']);
            }

            fclose($handle);

            return $header;
        }
        throw new LogicException('loadHeader error'); //TODO
    }
}
