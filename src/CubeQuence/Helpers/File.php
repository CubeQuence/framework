<?php

namespace CQ\Helpers;

use Exception;

class File
{
    private $file;
    private $file_path;

    /**
     * Open file
     *
     * @param string $path
     * @param bool $new
     */
    public function __construct($path)
    {
        $this->file = $this->open($path);
        $this->file_path = $path;
    }

    /**
     * Open file with specific mode
     *
     * @param string $path
     * @param string $mode
     *
     * @return resource
     */
    private function open($path, $mode = 'a+')
    {
        $handle = fopen($path, $mode);

        if (!$handle) {
            throw new Exception('Cannot open file');
        }

        return $handle;
    }

    /**
     * Read and return file contents
     *
     * @return string
     */
    public function read()
    {
        $data = fread(
            $this->file,
            filesize($this->file_path)
        );

        if (!$data) {
            throw new Exception('Cannot read file');
        }

        return $data;
    }

    /**
     * Write data to file
     *
     * @param string $data
     *
     * @return void
     */
    public function write($data)
    {
        $file = $this->open($this->file_path, 'w');

        if (!fwrite($file, $data)) {
            throw new Exception('Cannot write to file');
        }
    }

    /**
     * Append data to file
     *
     * @param string $data
     *
     * @return void
     */
    public function append($data)
    {
        if (!fwrite($this->file, $data)) {
            throw new Exception('Cannot append to file');
        }
    }

    /**
     * Close file
     *
     * @return void
     */
    public function close()
    {
        if (!fclose($this->file)) {
            throw new Exception('Cannot close file');
        }
    }

    /**
     * Delete file
     *
     * @return void
     */
    public function delete()
    {
        if (!unlink($this->file_path)) {
            throw new Exception('Cannot delete file');
        }
    }

    /**
     * Copy original file to opened file
     *
     * @param string $original_file
     *
     * @return void
     */
    public function copy($original_file)
    {
        if (!copy($original_file, $this->file_path)) {
            throw new Exception('Cannot copy file');
        }
    }

    /**
     * Get mime type
     *
     * @return array
     */
    public function getMimeType()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $this->file_path);
        finfo_close($finfo);

        return $mime_type;
    }
}
