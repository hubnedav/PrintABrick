<?php

namespace AppBundle\Service\Loader;

use AppBundle\Exception\FileNotFoundException;
use AppBundle\Exception\WriteErrorException;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;

abstract class BaseLoader
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

    /** @var Logger */
    protected $logger;

    /**
     * Loader constructor.
     *
     * @param EntityManager $em
     * @param Translator    $translator
     */
    public function setArguments(EntityManager $em, $logger)
    {
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->logger = $logger;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        $this->output->setDecorated(true);
    }

    /**
     * Initialize new progress bar.
     *
     * @param $total
     */
    protected function initProgressBar($total)
    {
        $this->progressBar = new ProgressBar($this->output, $total);
//        $this->progressBar->setFormat('very_verbose');
        $this->progressBar->setFormat('[%current%/%max%] [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) (%filename%)'.PHP_EOL);
        $this->progressBar->setBarWidth(70);
        $this->progressBar->start();
    }

    protected function progressCallback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        switch ($notification_code) {
            case STREAM_NOTIFY_FILE_SIZE_IS:
                $this->initProgressBar($bytes_max);
                $this->progressBar->setFormat('[%current%/%max%] [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%)'.PHP_EOL);
                break;
            case STREAM_NOTIFY_PROGRESS:
                $this->progressBar->setProgress($bytes_transferred);
                break;
            case STREAM_NOTIFY_COMPLETED:
                $this->progressBar->setMessage('<info>Done</info>');
                $this->progressBar->setProgress($bytes_max);
                $this->progressBar->finish();
                break;
        }
    }

    /**
     * Download file from $url, save it to system temp directory and return filepath.
     *
     * @param $url
     *
     * @throws FileNotFoundException
     *
     * @return bool|string
     */
    protected function downloadFile($url)
    {
        $this->output->writeln('Loading file from: <comment>'.$url.'</comment>');
        $temp = tempnam(sys_get_temp_dir(), 'printabrick.');

        $ctx = stream_context_create([], [
            'notification' => [$this, 'progressCallback'],
        ]);

        try {
            if (false === file_put_contents($temp, fopen($url, 'r', 0, $ctx))) {
                throw new WriteErrorException($temp);
            }
        } catch (ContextErrorException $e) {
            throw new FileNotFoundException($url);
        } catch (\Exception $e) {
            throw new LogicException($e);
        }

        return $temp;
    }
}
