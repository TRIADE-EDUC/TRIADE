<?php

namespace Pmb\Common\Helper;

class Directory
{
    public static function getDirectories(string $dirPath)
    {
        if (!is_dir($dirPath)) {
            return [];
        }

        $result = array();
        $dirPath = realpath($dirPath);
		$dirs = array_filter(glob("{$dirPath}/*"), 'is_dir');

		foreach ($dirs as $dir) {
			if (basename($dir) != "CVS") {
				$result[] = $dir;
			}
		}
		return $result;
    }

    public static function getNameDirectories(string $dirPath)
    {
        if (!is_dir($dirPath)) {
            return [];
        }
        return array_map("basename", static::getDirectories($dirPath));
    }
}