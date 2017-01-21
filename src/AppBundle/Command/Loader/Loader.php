<?php

namespace AppBundle\Command\Loader;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;

class Loader
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

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        $this->output->setDecorated(true);
    }

    private function progressCallback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        switch ($notification_code) {
            case STREAM_NOTIFY_FILE_SIZE_IS:
                $this->progressBar = new ProgressBar($this->output);
                $this->progressBar->setBarWidth(100);
                $this->progressBar->start($bytes_max);
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
