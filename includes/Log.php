<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author Peter Elmered
 */
class WP_PJAX_Log
{
    
    private $fh  = NULL; //Log file handle
    
    public function __construct()
    {
        
    }
    
    public function setFile($filename)
    {
        $this->fh = fopen(WP_PJAX_PLUGIN_PATH.'logs'.DIRECTORY_SEPARATOR.$filename, 'a');
    }
    
    
    public function write( $msg )
    {
        if( !$this->fh )
        {
            $this->setFile('error');
        }
        
        return fwrite($this->fh, $this->_format_log($msg));
    }
    
    private function  _format_log($msg)
    {
        return  '['.date('Y-m-d h:i:s').'] '. $msg . "\n\n";
    }
    
}

?>
