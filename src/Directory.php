<?php

declare(strict_types=1);

namespace Omexon\Filesystem;

use FilesystemIterator;

class Directory
{
    /**
     * Check if directory exists.
     *
     * @param string $path
     * @return bool
     */
    public static function exist(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Check if it is a directory.
     *
     * @param string|null $path
     * @return bool
     */
    public static function isDirectory(?string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Check if directory is writable.
     *
     * @param string $path
     * @return bool
     */
    public static function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    /**
     * Make directory.
     *
     * @param string $path
     * @param int $mode See mkdir() for options.
     */
    public static function make(string $path, int $mode = 0777): void
    {
        if (!is_dir($path)) {
            mkdir($path, $mode, true);
        }
    }

    /**
     * Delete.
     *
     * @param string $path
     * @param bool $preserveRoot Default false.
     * @return bool
     */
    public static function delete(?string $path, bool $preserveRoot = false): bool
    {
        if (!self::isDirectory($path)) {
            return false;
        }

        // Process entries in path recursively.
        $entries = new FilesystemIterator($path);
        foreach ($entries as $entry) {
            if ($entry->isDir() && !$entry->isLink()) {
                self::delete($entry->getPathname());
            } else {
                File::delete($entry->getPathname());
            }
        }

        // Remove root if not marked as preserve.
        if (!$preserveRoot) {
            @rmdir($path);
        }

        return true;
    }

    /**
     * Clean directory.
     *
     * @param string $path
     * @return bool
     */
    public static function clean(string $path): bool
    {
        return self::delete($path, true);
    }

    /**
     * Get temp directory.
     *
     * @return string
     */
    public static function temp(): string
    {
        return sys_get_temp_dir();
    }
}