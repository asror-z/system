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


use yii\i18n\Formatter;

class ZFormatter extends Formatter
{

	/**
	 *
	 * Function  formatBytes
	 * @param $bytes
	 * @param int $precision
	 * @param bool $bInterval
	 * @return  string
	 */
	public static function formatBytes($bytes, $precision = 2, $bInterval = true)
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units)-1);

		$bytes /= 1024 ** $pow;

		return round($bytes, $precision) . ' ' . $units[$pow];
	}


    public static function filterValue($value) {

        $return = $value;

        if (is_array($value)) {
            $return = [];
            foreach ($value as $key => $val) {
                if ((int)$val !== 0) {
                    $return[$key] = (int)$val;
                }
            }
        } else {
            if (intval($value) !== 0) {
                $return = (int)$value;
            }
        }

        return $return;

    }

    //start|DavlatovRavshan|2020.10.13

    public static function filter($data) {

        $process = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $process[$key] = ZVarDumper::checkValue($value);
            }
        } else {
            $process = ZVarDumper::checkValue($data);
        }

        return $process;
        
    }
    
    //end|DavlatovRavshan|2020.10.13



}
