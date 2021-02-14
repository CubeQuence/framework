<?php

namespace CQ\Helpers;

use ParagonIE\Halite\File as FileLib;
use CQ\Crypto\Symmetric;
use CQ\Crypto\Asymmetric;

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
     * @throws \Exception
     */
    private function open($mode)
    {
        $handle = fopen($this->file_path, $mode);

        if (!$handle) {
            throw new \Exception(message: 'Cannot open file');
        }

        $this->file = $handle;
    }

    /**
     * Close file
     *
     * @return void
     * @throws \Exception
     */
    private function close()
    {
        if (!fclose($this->file)) {
            throw new \Exception(message: 'Cannot close file');
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

        try {
            $data = fread(
                $this->file,
                filesize($this->file_path)
            );

            $this->close();
        } catch (\Throwable $e) {
            return "";
        }

        return $data;
    }

    /**
     * Write data to file
     *
     * @param string $data
     *
     * @return void
     * @throws \Exception
     */
    public function write($data)
    {
        $this->open('w');

        if (!fwrite($this->file, $data)) {
            throw new \Exception(message: 'Cannot write to file');
        }

        $this->close();
    }

    /**
     * Append data to file
     *
     * @param string $data
     *
     * @return void
     * @throws \Exception
     */
    public function append($data)
    {
        $this->open('a');

        if (!fwrite($this->file, $data)) {
            throw new \Exception(message: 'Cannot append to file');
        }

        $this->close();
    }

    /**
     * Delete file
     *
     * @return void
     * @throws \Exception
     */
    public function delete()
    {
        if (!unlink($this->file_path)) {
            throw new \Exception(message: 'Cannot delete file');
        }
    }

    /**
     * Copy original file to opened file
     *
     * @param string $original_file
     *
     * @return void
     * @throws \Exception
     */
    public function copy($original_file)
    {
        if (!copy($original_file, $this->file_path)) {
            throw new \Exception(message: 'Cannot copy file');
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

    /**
     * Get path info
     *
     * @return array
     */
    public function getPathInfo()
    {
        return pathinfo($this->file_path);
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return FileLib::checksum($this->file_path);
    }

    /**
     * Encrypt file to output
     *
     * @param string $outputLocation
     * @param string $mode
     * @param string $key optional
     *
     * @return void
     * @throws \Exception
     */
    public function encrypt($outputLocation, $mode, $key = null)
    {
        if ($mode === 'symmetric') {
            $key = Symmetric::getKey($key, 'encryption');

            return FileLib::encrypt(
                $this->file_path,
                $outputLocation,
                $key
            );
        }

        if ($mode === 'asymmetric') {
            $enc_public_key = Asymmetric::getKey($key, 'encryption', 'public');

            return FileLib::seal(
                $this->file_path,
                $outputLocation,
                $enc_public_key
            );
        }

        throw new \Exception(message: 'Invalid key type!');
    }

    /**
     * Decrypt file to output
     *
     * @param string $outputLocation
     * @param string $mode
     * @param string $key optional
     *
     * @return void
     * @throws \Exception
     */
    public function decrypt($outputLocation, $mode, $key = null)
    {
        if ($mode === 'symmetric') {
            $key = Symmetric::getKey($key, 'encryption');

            return FileLib::decrypt(
                $this->file_path,
                $outputLocation,
                $key
            );
        }

        if ($mode === 'asymmetric') {
            $enc_private_key = Asymmetric::getKey($key, 'encryption', 'private');

            return FileLib::unseal(
                $this->file_path,
                $outputLocation,
                $enc_private_key
            );
        }

        throw new \Exception(message: 'Invalid key type!');
    }

    /**
     * Sign file using private key
     *
     * @param string $private_key
     *
     * @return string
     */
    public function sign($auth_private_key)
    {
        $auth_private_key = Asymmetric::getKey($auth_private_key, 'authentication', 'private');

        return FileLib::sign(
            $this->file_path,
            $auth_private_key
        );
    }

    /**
     * Verify file signature using public key
     *
     * @param string $signature
     * @param string $public_key
     *
     * @return bool
     */
    public function verify($signature, $auth_public_key)
    {
        $auth_public_key = Asymmetric::getKey($auth_public_key, 'authentication', 'public');

        return FileLib::verify(
            $this->file_path,
            $auth_public_key,
            $signature
        );
    }
}
