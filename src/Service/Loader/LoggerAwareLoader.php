<?php

namespace App\Service\Loader;

use App\Exception\WriteErrorException;
use App\Transformer\FormatTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\StyleInterface;

abstract class LoggerAwareLoader
{
    protected StyleInterface $output;
    protected ProgressBar $progressBar;
    protected LoggerInterface $logger;

    private FormatTransformer $formatTransformer;

    /**
     * BaseLoader constructor.
     */
    public function __construct()
    {
        $this->formatTransformer = new FormatTransformer();
    }

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setOutput(StyleInterface $output)
    {
        $this->output = $output;
        $this->output->setDecorated(true);
    }

    /**
     * Initialize new progress bar.
     *
     * @param $total
     * @param string $format
     */
    protected function initProgressBar($total, $format = 'loader')
    {
        $this->progressBar = $this->output->createProgressBar($total);
        ProgressBar::setFormatDefinition('loader', '[%current% / %max%] [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) (%message%)'.PHP_EOL);
        ProgressBar::setFormatDefinition('download', '[%progress% / %size%] [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%)'.PHP_EOL);
        $this->progressBar->setFormat($format);
        $this->progressBar->setBarWidth(70);

        $this->progressBar->start();
    }

    /**
     * @codeCoverageIgnore
     */
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
                if ($this->progressBar) {
                    $this->progressBar->setProgress($bytes_transferred);
                    $this->progressBar->setMessage(
                        $this->formatTransformer->bytesToSize($bytes_transferred),
                        'progress'
                    );
                }
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
     * @throws WriteErrorException
     *
     * @return bool|string
     */
    protected function downloadFile($url)
    {
        $this->output->writeln(['Loading file from: <comment>'.$url.'</comment>']);
        $temp = tempnam(sys_get_temp_dir(), 'printabrick.');

        $ctx = stream_context_create([], [
            'notification' => [$this, 'progressCallback'],
        ]);

        if (false === file_put_contents($temp, fopen($url, 'rb', 0, $ctx))) {
            throw new WriteErrorException($temp);
        }

        return $temp;
    }

    /**
     * Download file from $url, save it to system temp directory and return filepath.
     *
     * @param $url
     *
     * @throws WriteErrorException
     *
     * @return bool|string
     */
    protected function downloadGzFile($url)
    {
        $this->output->writeln(['Loading file from: <comment>'.$url.'</comment>']);
        $temp = tempnam(sys_get_temp_dir(), 'printabrick.');

        if (false === file_put_contents($temp, gzopen($url, 'rb', 0))) {
            throw new WriteErrorException($temp);
        }

        return $temp;
    }
}
