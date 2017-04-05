<?php

namespace AppBundle\Service\Loader;

use AppBundle\Utils\RelationMapper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;

abstract class BaseLoaderService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    /** @var RelationMapper */
    protected $relationMapper;

    /**
     * Loader constructor.
     *
     * @param EntityManager $em
     * @param Translator    $translator
     */
    public function setArguments(EntityManager $em, $relationMapper)
    {
        $this->em = $em;
        $this->relationMapper = $relationMapper;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        $this->output->setDecorated(true);
    }

    protected function initProgressBar($total)
    {
        $this->progressBar = new ProgressBar($this->output, $total);
        $this->progressBar->setFormat('very_verbose');
        $this->progressBar->setFormat('%current%/%max% [%bar%]%percent:3s%% (%elapsed:6s%/%estimated:-6s%)'.PHP_EOL);
        $this->progressBar->start();
    }

    protected function progressCallback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        switch ($notification_code) {
            case STREAM_NOTIFY_FILE_SIZE_IS:
                $this->initProgressBar($bytes_max);
                break;
            case STREAM_NOTIFY_PROGRESS:
                $this->progressBar->setProgress($bytes_transferred);
                break;
            case STREAM_NOTIFY_COMPLETED:
                $this->progressBar->setProgress($bytes_transferred);
                $this->progressBar->finish();
                break;
        }
    }

    protected function downloadFile($url)
    {
        $this->output->writeln('Downloading file from: <info>'.$url.'</info>');
        $temp = tempnam(sys_get_temp_dir(), 'printabrick.');

        $ctx = stream_context_create([], [
            'notification' => [$this, 'progressCallback'],
        ]);

        try {
            if (false === file_put_contents($temp, fopen($url, 'r', 0, $ctx))) {
                throw new LogicException('error writing file'); //TODO
            }
        } catch (ContextErrorException $e) {
            throw new LogicException('wrong url'); //TODO
        } catch (\Exception $e) {
            throw new LogicException('exception:  '.$e->getMessage()); //TODO
        }

        return $temp;
    }
}
