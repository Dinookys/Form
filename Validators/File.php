<?php

namespace Validators;


use Classes\Validator;

class File extends Validator
{
    protected $message = 'Arquivo invalído';
    protected $messages = '';
    protected $invalidFiles = [];
    protected $extensions = array(
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.oasis.opendocument.spreadsheet',
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
    );
    protected $minSize = 1;
    protected $maxSize = 0;

    public function __construct()
    {
        $upload_max_filesize = ini_get("upload_max_filesize");

        if(\preg_match('/[\w]/', $upload_max_filesize)) {
            $upload_max_filesize = (int) preg_replace('/[^\d]/','', $upload_max_filesize) * 1024 * 1024;            

        }

        $this->maxSize = $upload_max_filesize;

    }

    public function validation($file, \Form\Form $instance, $id = null)
    {  

        if (!is_array($file)) {
            return false;
        }

        if (is_array($file['size'])) {

            foreach ($file['error'] as $key => $error) {
                if($error != 0) {
                    return false;
                }
            }

            foreach ($file['size'] as $key => $size) {

                if ($size < $this->getMinSize() || $size > $this->getMaxSize()) {
                    $this->setErrorSize();
                    return false;
                } 

                if (in_array($file['type'][$key], $this->extensions)) {
                    $this->setErrorType();
                    return false;
                }      
            }

            return true;
        }
        
        if($file['error'] != 0) {
            return false;
        }

        if ($file['size'] < $this->getMinSize() || $file['size'] > $this->getMaxSize()) {
            $this->setErrorSize();
            return false;
        }

        if (!in_array($file['type'], $this->extensions)) {
            $this->setErrorType();
            return false;
        } 

        return true;
    }

    /***
     * Set min and max filesize in MB
     */
    public function setFileSize($min = 0, $max = 0)
    {
        $this->minSize = $min * 1024 * 1024;
        $this->maxSize = $max * 1024 * 1024;
    }

    /***
     * Set valid extensions separated for comma
     * ex: image/jpeg,image/png,image/gif,image/vnd.adobe.photoshop
     */
    public function setExtensions($extensions = '')
    {
        if ($extensions) {
            $this->extensions = explode(',', $extensions);
        }
    }

    public function getMinSize()
    {
        return $this->minSize;
    }

    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function setErrorSize($message = null)
    {
        $message = $message ?? 'Tamanho do arquivo deve entre %s e %sMB';
        $this->setMessage(sprintf($message, floor($this->minSize/1024), $this->maxSize/1024));
    }

    public function setErrorType($message = null, $replace = null)
    {
        $message = $message ?? 'Arquivo no formato inválido';
        $this->setMessage(sprintf($message, $replace));
    }
}
