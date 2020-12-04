<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\helpers;


use yii\base\InvalidArgumentException;
use yii\helpers\FileHelper;

class ZFileHelper extends FileHelper
{

    #region Scan

    public static function scanFolder($path, bool $recursive = false)
    {
        $excludeFolders = [
            '.idea',
            '.trash',
            '@',
        ];

        $dirs = self::findDirectories($path, [
            'recursive' => $recursive
        ]);
        foreach ($dirs as $index => $dir) {
            $bor = false;
            foreach ($excludeFolders as $excludeFolder) {
                if (strpos($dir, $excludeFolder) !== false)
                    $bor = true;
            }
            if ($bor)
                unset($dirs[$index]);
        }
        return $dirs;
    }

    public static function scanFiles($path, bool $recursive = false, array $extensions = [], array $except = [])
    {
        $opts = [
            'recursive' => $recursive
        ];
        
        if (!empty($extensions))
            $opts = ZArrayHelper::merge($opts, [
                'only' => $extensions,
               // 'except' => $except,
            ]);

        return self::findFiles($path, $opts);
    }


    public static function scanFilesPHP($path, bool $recursive = false)
    {
        return self::scanFiles($path, $recursive,
            ['*.php'],
            ['@']);
    }


    #endregion

    public static function extension($file)
    {
        $item = pathinfo($file);
        $ext = $item['extension'];
        $ext = str_replace('.', '', $ext);
        return $ext;
    }


    public static function normLinux($path)
    {
        return parent::normalizePath($path, '/');
    }


    public static function normWindows($path)
    {
        return parent::normalizePath($path, '\\');
    }


    public static function replaceInPath($search, $replace, $path, $extension = '*.php')
    {

        $files = self::findFiles($path, [
            'only' => [$extension]
        ]);

        if (!empty($files))
            foreach ($files as $file) {
                self::replaceInFile($search, $replace, $file);
            }

    }

    public static function replaceInFile($search, $replace, $file)
    {
        $text = file_get_contents($file);
        $text = str_replace($search, $replace, $text);
        file_put_contents($file, $text);
    }

    public static function toStream(string $str)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $str);
        rewind($stream);
        return $stream;
    }

    public static function removeDir($dir, $options = [])
    {
        if (!is_dir($dir)) {
            return null;
        }
        if (!empty($options['traverseSymlinks']) || !is_link($dir)) {
            if (!($handle = opendir($dir))) {
                return null;
            }
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    static::removeDir($path, $options);
                } else {
                    static::unlink($path);
                }
            }
            closedir($handle);
        }
        if (is_link($dir)) {
            static::unlink($dir);
        } else {
            try {
                rmdir($dir);
            } catch (\Exception $e) {

            }
        }
    }

    public static function cloneDir($src, $dst, $newName = '', $options = [])
    {
        $src = static::normalizePath($src);
        $dst = static::normalizePath($dst);

        if ($src === $dst || strpos($dst, $src . DIRECTORY_SEPARATOR) === 0) {
            throw new InvalidArgumentException('Trying to copy a directory to itself or a subdirectory.');
        }
        $dstExists = is_dir($dst);
        if (!$dstExists && (!isset($options['copyEmptyDirectories']) || $options['copyEmptyDirectories'])) {
            static::createDirectory($dst, isset($options['dirMode']) ? $options['dirMode'] : 0775, true);
            $dstExists = true;
        }

        $handle = opendir($src);
        if ($handle === false) {
            throw new InvalidArgumentException("Unable to open directory: $src");
        }
        if (!isset($options['basePath'])) {
            // this should be done only once
            $options['basePath'] = realpath($src);
            $options = static::normalizeOptions($options);
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $from = $src . DIRECTORY_SEPARATOR . $file;
            $to = $dst . DIRECTORY_SEPARATOR . $file;
            if (static::filterPath($from, $options)) {
                if (isset($options['beforeCopy']) && !call_user_func($options['beforeCopy'], $from, $to)) {
                    continue;
                }
                if (is_file($from)) {
                    if (!$dstExists) {
                        // delay creation of destination directory until the first file is copied to avoid creating empty directories
                        static::createDirectory($dst, isset($options['dirMode']) ? $options['dirMode'] : 0775, true);
                        $dstExists = true;
                    }
                    copy($from, $to);
                    if (isset($options['fileMode'])) {
                        @chmod($to, $options['fileMode']);
                    }
                } else {
                    // recursive copy, defaults to true
                    if (!isset($options['recursive']) || $options['recursive']) {
                        static::copyDirectory($from, $to, $options);
                    }
                }
                if (isset($options['afterCopy'])) {
                    call_user_func($options['afterCopy'], $from, $to);
                }
            }
        }
        closedir($handle);
    }
}
