<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\tool;


class Str
{
	/**
	 * 下划线转驼峰
	 * @param $str
	 * @param bool $ucfirst
	 * @return string|string[]
	 */
	public static function convertUnderline ( $str , $ucfirst = true)
	{
		$str = ucwords(str_replace('_', ' ', $str));
		$str = str_replace(' ','',lcfirst($str));
		return $ucfirst ? ucfirst($str) : $str;
	}

	/**
	 * 驼峰转下划线
	 * @param $camelCaps
	 * @param string $separator
	 * @return string
	 */
	public static function uncamelize($camelCaps,$separator='_'){
		return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
	}
}