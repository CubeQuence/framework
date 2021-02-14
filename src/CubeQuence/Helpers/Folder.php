<?php

namespace CQ\Helpers;

class Folder
{
    /**
     * Create folder
     *
     * @param string $directory
     *
     * @return void
     * @throws \Exception
     */
    public static function create($directory, $mode = 0770, $recursive = true)
    {
        if (self::exists($directory)) {
            return;
        }

        if (!mkdir($directory, $mode, $recursive)) {
            throw new \Exception(message: 'Folder could not be created');
        }
    }

    /**
     * Delete folder
     *
     * @param string $directory
     * @param bool $recursive false
     *
     * @return void
     * @throws \Exception
     */
    public static function delete($directory, $recursive = false)
    {
        if (!self::exists($directory)) {
            return;
        }

        if ($recursive) {
            foreach (glob("{$directory}/*") as $file) {
                if (is_dir($file)) {
                    self::delete($file, true);
                } else {
                    unlink($file);
                }
            }
        }

        if (!rmdir($directory)) {
            throw new \Exception(message: 'Folder could not be deleted');
        }
    }

    /**
     * Check if directory exists
     *
     * @param string $directory
     *
     * @return bool
     */
    public static function exists($directory)
    {
        return is_dir($directory);
    }

    /**
     * Scan files in directory
     *
     * @param string $directory
     *
     * @return array
     */
    public static function getContents($directory)
    {
        $data = scandir($directory);

        array_shift($data);
        array_shift($data);

        return $data;
    }
}
