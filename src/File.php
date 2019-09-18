<?php

declare(strict_types=1);

namespace Omexon\Filesystem;

class File
{
    /**
     * Get.
     *
     * @param string $filename
     * @param null $default
     * @return string|null
     */
    public static function get(string $filename, $default = null): ?string
    {
        if (!self::exist($filename)) {
            return $default;
        }
        return file_get_contents($filename);
    }

    /**
     * Put.
     *
     * @param string $filename
     * @param string $content
     * @return int|null
     */
    public static function put(string $filename, string $content): ?int
    {
        $bytes = file_put_contents($filename, $content);
        return is_int($bytes) ? $bytes : null;
    }

    /**
     * Append.
     *
     * @param string $filename
     * @param string $content
     * @return int|null
     */
    public static function append(string $filename, string $content): ?int
    {
        return file_put_contents($filename, $content, FILE_APPEND);
    }

    /**
     * Prepend.
     *
     * @param string $filename
     * @param string $content
     * @return int|null
     */
    public static function prepend(string $filename, string $content): ?int
    {
        if (self::exist($filename)) {
            return self::put($filename, $content . self::get($filename));
        }
        return self::put($filename, $content);
    }

    /**
     * Get lines.
     *
     * @param string $filename
     * @param string[] $default
     * @return string[]
     */
    public static function getLines(string $filename, array $default = []): array
    {
        $content = self::get($filename);
        if ($content === null) {
            return $default;
        }
        $content = str_replace("\r", '', (string)$content);
        return explode("\n", rtrim($content));
    }

    /**
     * Put line.
     *
     * @param string $filename
     * @param string $content
     * @param string $separator Default "\n".
     * @return int|null
     */
    public static function putLine(string $filename, string $content, string $separator = "\n"): ?int
    {
        return self::putLines($filename, [$content], $separator);
    }

    /**
     * Put lines.
     *
     * @param string $filename
     * @param string[] $lines
     * @param string $separator Default "\n".
     * @return int|null
     */
    public static function putLines(string $filename, array $lines, string $separator = "\n"): ?int
    {
        return self::put($filename, implode($separator, $lines) . $separator);
    }

    /**
     * Append line.
     *
     * @param string $filename
     * @param string $content
     * @param string $separator Default "\n".
     * @return int|null
     */
    public static function appendLine(string $filename, string $content, string $separator = "\n"): ?int
    {
        return self::appendLines($filename, [$content], $separator);
    }

    /**
     * Append lines.
     *
     * @param string $filename
     * @param string[] $lines
     * @param string $separator Default "\n".
     * @return int|null
     */
    public static function appendLines(string $filename, array $lines, string $separator = "\n"): ?int
    {
        return self::append($filename, implode($separator, $lines) . $separator);
    }

    /**
     * Prepend line.
     *
     * @param string $filename
     * @param string $content
     * @param string $separator Default "\n".
     * @return int|null
     */
    public static function prependLine(string $filename, string $content, string $separator = "\n"): ?int
    {
        return self::prependLines($filename, [$content], $separator);
    }

    /**
     * Prepend lines.
     *
     * @param string $filename
     * @param string[] $lines
     * @param string $separator Default "\n".
     * @return int|null
     */
    public static function prependLines(string $filename, array $lines, string $separator = "\n"): ?int
    {
        return self::prepend($filename, implode($separator, $lines) . $separator);
    }

    /**
     * Read json.
     *
     * @param string $filename
     * @param mixed[] $default
     * @return mixed[]
     */
    public static function getJson(string $filename, array $default = []): array
    {
        $content = self::get($filename);
        if ($content === null) {
            return $default;
        }
        $data = json_decode($content, true);
        if ($data === null || $data === false) {
            $data = [];
        }
        return $data;
    }

    /**
     * Save json.
     *
     * @param string $filename
     * @param mixed[] $data
     * @param bool $prettyPrint Default true.
     * @return int|null
     */
    public static function putJson(string $filename, array $data, bool $prettyPrint = true): ?int
    {
        $options = JSON_UNESCAPED_SLASHES;
        if ($prettyPrint) {
            $options += JSON_PRETTY_PRINT;
        }
        $data = json_encode($data, $options);
        return self::put($filename, $data);
    }

    /**
     * Check if file exist.
     *
     * @param string $filename
     * @return bool
     */
    public static function exist(string $filename): bool
    {
        return file_exists($filename);
    }

    /**
     * Touch.
     *
     * @param string $filename
     * @param int $time Default null which means current.
     */
    public static function touch(string $filename, ?int $time = null): void
    {
        if ($time === null) {
            $time = time();
        }
        touch($filename, $time);
    }

    /**
     * Copy.
     *
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public static function copy(string $filename, string $path): bool
    {
        $basename = self::basename($filename);
        if (substr($path, -strlen($basename)) !== $basename) {
            $path = rtrim($path, '/') . '/' . self::basename($filename);
        }
        return @copy($filename, $path);
    }

    /**
     * Move.
     *
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public static function move(string $filename, string $path): bool
    {
        $basename = self::basename($filename);
        if (substr($path, -strlen($basename)) !== $basename) {
            $path = rtrim($path, '/') . '/' . self::basename($filename);
        }
        return @rename($filename, $path);
    }

    /**
     * Delete file.
     *
     * @param string $filename
     * @return bool
     */
    public static function delete(string $filename): bool
    {
        return @unlink($filename);
    }

    /**
     * Filename.
     *
     * @param string $path
     * @return string
     */
    public static function filename(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Basename.
     *
     * @param string $path
     * @return string
     */
    public static function basename(string $path): string
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Dirname.
     *
     * @param string $path
     * @return string
     */
    public static function dirname(string $path): string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Extension.
     *
     * @param string $path
     * @return string
     */
    public static function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Type.
     *
     * @param string $path
     * @return string
     */
    public static function type(string $path): string
    {
        $result = @filetype($path);
        return $result !== false ? $result : '';
    }

    /**
     * Mimetype.
     *
     * @param string $path
     * @return string
     */
    public static function mimetype(string $path): string
    {
        $result = @finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
        return $result !== false ? $result : '';
    }

    /**
     * Size.
     *
     * @param string $path
     * @return int
     */
    public static function size(string $path): int
    {
        return filesize($path);
    }

    /**
     * Last modified.
     *
     * @param string $path
     * @return int
     */
    public static function lastModified(string $path): int
    {
        clearstatcache();
        return filemtime($path);
    }

    /**
     * Is file.
     *
     * @param string $path
     * @return bool
     */
    public static function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Create and get temp filename.
     *
     * @param string $path Default '' which means sys_get_temp_dir().
     * @param string $prefix Default ''.
     * @param string $extension
     * @return string
     */
    public static function getTempFilename(string $path = '', string $prefix = '', string $extension = ''): string
    {
        if ($path === '') {
            $path = sys_get_temp_dir();
        }

        // Create unique basename.
        $filename = md5((string)mt_rand());
        if ($prefix !== '') {
            $filename = $prefix . $filename;
        }
        if ($extension !== '') {
            $filename .= $extension;
        }

        if (is_dir($path)) {
            touch($path . '/' . $filename);
        }
        return $path . '/' . $filename;
    }
}