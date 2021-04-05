<?php

declare(strict_types=1);

namespace CQ\Helpers;

class Folder
{
    /**
     * Create folder
     *
     * @throws \Exception
     */
    public static function create(string $directory, int $mode = 0770, bool $recursive = true): void
    {
        if (self::exists(directory: $directory)) {
            return;
        }

        $mkdir_success = mkdir(
            pathname: $directory,
            mode: $mode,
            recursive: $recursive
        );

        if (! $mkdir_success) {
            throw new \Exception(message: 'Folder could not be created');
        }
    }

    /**
     * Delete folder
     *
     * @param bool $recursive false
     *
     * @throws \Exception
     */
    public static function delete(string $directory, bool $recursive = false): void
    {
        if (! self::exists(directory: $directory)) {
            return;
        }

        if ($recursive) {
            foreach (glob(pattern: "{$directory}/*") as $file) {
                if (is_dir(filename: $file)) {
                    self::delete(
                        directory: $file,
                        recursive: true
                    );
                } else {
                    unlink(filename: $file);
                }
            }
        }

        if (! rmdir(dirname: $directory)) {
            throw new \Exception(message: 'Folder could not be deleted');
        }
    }

    /**
     * Check if directory exists
     */
    // TODO: maybe replace Folder::exists with is_dir()
    public static function exists(string $directory): bool
    {
        return is_dir(filename: $directory);
    }

    /**
     * Scan files in directory
     *
     * @return array
     */
    // TODO: maybe rename to getFiles
    public static function getContents(string $directory): array
    {
        $data = scandir(directory: $directory);

        array_shift(array: $data);
        array_shift(array: $data);

        return $data;
    }
}
