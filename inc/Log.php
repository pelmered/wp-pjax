<?php

/**
 * Class WP_PJAX_Log
 *
 * @author Peter Elmered
 */
class WP_PJAX_Log
{

    /**
     * Log file handle
     *
     * @var resource|null
     */
    private $fh = null;

    public function write($msg)
    {
        if (!$this->fh) {
            $this->setFile('error');
        }

        return fwrite($this->fh, $this->formatLog($msg));
    }

    public function setFile($filename)
    {
        $this->fh = fopen(WP_PJAX_PLUGIN_PATH . 'logs' . DIRECTORY_SEPARATOR . $filename, 'a');
    }

    private function formatLog($msg)
    {
        return '[' . date('Y-m-d h:i:s') . '] ' . $msg . "\n\n";
    }
}
