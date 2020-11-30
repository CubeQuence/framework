<?php

namespace CQ\Helpers;

use Exception;

class File
{
    private $file;
    private $file_path;

    /**
     * Set path
     *
     * @param string $path
     * @param bool $new
     */
    public function __construct($path)
    {
        $this->file_path = $path;
    }

    /**
     * Open file with specific mode
     *
     * @param string $mode
     */
    private function open($mode)
    {
        $handle = fopen($this->file_path, $mode);

        if (!$handle) {
            throw new Exception('Cannot open file');
        }

        $this->file = $handle;
    }

    /**
     * Close file
     *
     * @return void
     */
    private function close()
    {
        if (!fclose($this->file)) {
            throw new Exception('Cannot close file');
        }
    }

    /**
     * Create file
     *
     * @return void
     */
    public function create()
    {
        $this->open('c');
        $this->close();
    }

    /**
     * Read and return file contents
     *
     * @return string
     */
    public function read()
    {
        $this->open('r');

        $data = fread(
            $this->file,
            filesize($this->file_path)
        );

        $this->close();

        if ($data === false) {
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
        $this->open('w');

        if (!fwrite($this->file, $data)) {
            throw new Exception('Cannot write to file');
        }

        $this->close();
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
        $this->open('a');

        if (!fwrite($this->file, $data)) {
            throw new Exception('Cannot append to file');
        }

        $this->close();
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
     * @return string
     */
    public function getMimeType()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $this->file_path);
        finfo_close($finfo);

        return $mime_type;
    }
}
