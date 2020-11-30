<?php

namespace CQ\Helpers;

use Exception;

class File
{
    private $file;
    private $file_path;

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
        if (!self::exists($path)) {
            throw new Exception('File does not exist');
        }

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
     * Check if file exists
     *
     * @param string $file_path
     *
     * @return bool
     */
    public static function exists($file_path)
    {
        return file_exists($file_path);
    }

    /**
     * Copy file
     *
     * @param string $original_file
     * @param string $new_file
     *
     * @return void
     */
    public static function copy($original_file, $new_file)
    {
        if (!self::exists($original_file)) {
            throw new Exception('File does not exist');
        }

        if (!copy($original_file, $new_file)) {
            throw new Exception('Cannot copy file');
        }
    }

    /**
     * Get mime type
     *
     * @param string $file_path
     *
     * @return array
     */
    public static function getMimeType($file_path)
    {
        if (!self::exists($file_path)) {
            throw new Exception('File does not exist');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $data = finfo_file($finfo, $file_path);
        finfo_close($finfo);

        return $data;
    }
}
