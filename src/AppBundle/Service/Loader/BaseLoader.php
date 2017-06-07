<?php

namespace AppBundle\Service\Loader;

use AppBundle\Exception\FileNotFoundException;
use AppBundle\Exception\WriteErrorException;
use AppBundle\Transformer\FormatTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var FormatTransformer
     */
    private $formatTransformer;

    /**
     * BaseLoader constructor.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->formatTransformer = new FormatTransformer();
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
    protected function initProgressBar($total, $format = 'loader')
    {
        $this->progressBar = new ProgressBar($this->output, $total);
        ProgressBar::setFormatDefinition('loader', '[%current% / %max%] [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) (%message%)'.PHP_EOL);
        ProgressBar::setFormatDefinition('download', '[%progress% / %size%] [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%)'.PHP_EOL);
        $this->progressBar->setFormat($format);
        $this->progressBar->setBarWidth(70);

        $this->progressBar->start();
    }

    protected function progressCallback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        switch ($notification_code) {
            case STREAM_NOTIFY_FILE_SIZE_IS:
                $this->initProgressBar($bytes_max);
                $this->progressBar->setFormat('download');
                $this->progressBar->setMessage($this->formatTransformer->bytesToSize($bytes_max), 'size');
                $this->progressBar->setRedrawFrequency(1024 * 1024);
                break;
            case STREAM_NOTIFY_PROGRESS:
                $this->progressBar->setProgress($bytes_transferred);
                $this->progressBar->setMessage($this->formatTransformer->bytesToSize($bytes_transferred), 'progress');
                break;
            case STREAM_NOTIFY_COMPLETED:
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

    protected function writeOutput(array $lines)
    {
        if ($this->output) {
            $this->output->writeln($lines);
        }
    }
}
