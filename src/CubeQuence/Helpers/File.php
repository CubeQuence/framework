<?php

declare(strict_types=1);

namespace CQ\Helpers;

use CQ\Crypto\Asymmetric;
use CQ\Crypto\Symmetric;
use ParagonIE\Halite\File as FileCrypto;

class File
{
    private $file;

    /**
     * Set path
     *
     * @param string $file_path
     */
    public function __construct(
        private string $file_path
    ) {
    }

    /**
     * Create file
     */
    public function create(): void
    {
        $this->open(mode: 'c');
        $this->close();
    }

    /**
     * Read and return file contents
     */
    public function read(): string
    {
        $this->open(mode: 'r');

        try {
            $data = fread(
                handle: $this->file,
                length: filesize(filename: $this->file_path)
            );

            $this->close();
        } catch (\Throwable) {
            // TODO: maybe throw exception

            return '';
        }

        return $data;
    }

    /**
     * Write data to file
     *
     * @throws \Exception
     */
    public function write(string $data): void
    {
        $this->open(mode: 'w');

        if (! fwrite(handle: $this->file, string: $data)) {
            throw new \Exception(message: 'Cannot write to file');
        }

        $this->close();
    }

    /**
     * Append data to file
     *
     * @throws \Exception
     */
    // TODO: maybe also return new file content
    public function append(string $data): void
    {
        $this->open(mode: 'a');

        if (! fwrite(handle: $this->file, string: $data)) {
            throw new \Exception(message: 'Cannot append to file');
        }

        $this->close();
    }

    /**
     * Delete file
     *
     * @throws \Exception
     */
    public function delete(): void
    {
        if (! unlink(filename: $this->file_path)) {
            throw new \Exception(message: 'Cannot delete file');
        }
    }

    /**
     * Copy original file to opened file
     *
     * @throws \Exception
     */
    // TODO: rename to createFromOriginal
    public function copy(string $original_file): void
    {
        if (! copy(source: $original_file, dest: $this->file_path)) {
            throw new \Exception(message: 'Cannot copy file');
        }
    }

    /**
     * Get mime type
     */
    public function getMimeType(): string
    {
        $finfo = finfo_open(options: FILEINFO_MIME_TYPE);
        $mime_type = finfo_file(finfo: $finfo, file_name: $this->file_path);
        finfo_close(finfo: $finfo);

        return $mime_type;
    }

    /**
     * Get path info
     *
     * @return array
     */
    public function getPathInfo(): array
    {
        return pathinfo(path: $this->file_path);
    }

    /**
     * Get checksum
     */
    public function getChecksum(): string
    {
        return FileCrypto::checksum(filePath: $this->file_path);
    }

    /**
     * Encrypt file to output
     *
     * @param string $key optional
     *
     * @throws \Exception
     */
    public function encrypt(string $outputLocation, string $mode, ?string $key = null): int
    {
        if ($mode === 'symmetric') {
            $key = Symmetric::getKey(type: 'encryption', key: $key);

            return FileCrypto::encrypt(
                input: $this->file_path,
                output: $outputLocation,
                key: $key
            );
        }

        if ($mode === 'asymmetric') {
            $enc_public_key = Asymmetric::getKey(
                key: $key,
                type: 'encryption',
                scope: 'public'
            );

            return FileCrypto::seal(
                input: $this->file_path,
                output: $outputLocation,
                publicKey: $enc_public_key
            );
        }

        throw new \Exception(message: 'Invalid key type!');
    }

    /**
     * Decrypt file to output
     *
     * @param string $key optional
     *
     * @throws \Exception
     */
    public function decrypt(string $outputLocation, string $mode, ?string $key = null): bool
    {
        if ($mode === 'symmetric') {
            $key = Symmetric::getKey(
                type:'encryption',
                key: $key
            );

            return FileCrypto::decrypt(
                input: $this->file_path,
                output: $outputLocation,
                key: $key
            );
        }

        if ($mode === 'asymmetric') {
            $enc_private_key = Asymmetric::getKey(
                key: $key,
                type: 'encryption',
                scope: 'private'
            );

            return FileCrypto::unseal(
                input: $this->file_path,
                output: $outputLocation,
                secretKey: $enc_private_key
            );
        }

        throw new \Exception(message: 'Invalid key type!');
    }

    /**
     * Sign file using private key
     *
     * @param string $private_key
     */
    public function sign(string $auth_private_key): string
    {
        $auth_private_key = Asymmetric::getKey(
            key: $auth_private_key,
            type: 'authentication',
            scope: 'private'
        );

        return FileCrypto::sign(
            filename: $this->file_path,
            secretKey: $auth_private_key
        );
    }

    /**
     * Verify file signature using public key
     *
     * @param string $public_key
     */
    public function verify(string $signature, string $auth_public_key): bool
    {
        $auth_public_key = Asymmetric::getKey(
            key: $auth_public_key,
            type: 'authentication',
            scope: 'public'
        );

        return FileCrypto::verify(
            filename: $this->file_path,
            publicKey: $auth_public_key,
            signature: $signature
        );
    }

    /**
     * Open file with specific mode
     *
     * @throws \Exception
     */
    private function open(string $mode): void
    {
        $handle = fopen(
            filename: $this->file_path,
            mode: $mode
        );

        if (! $handle) {
            throw new \Exception(message: 'Cannot open file');
        }

        $this->file = $handle;
    }

    /**
     * Close file
     *
     * @throws \Exception
     */
    private function close(): void
    {
        if (! fclose(handle: $this->file)) {
            throw new \Exception(message: 'Cannot close file');
        }
    }
}
