<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

include dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use doc\Main;
$arr = [
	'basic_controller' => AAA::class, //基类
	'ignore' => [], //需要忽略的类
	'work_dir' => __FILE__,//工作目录
	'pattern_flag' => GLOB_ERR,//glob匹配形式
	'pattern' => 'current',//扫描模式
	'regular' => [],//各个类型的正则
	'memi_type' => '',//文件的memi值
	'deposit_path' => 'a',//保存的路径
];
$main = new Main($arr);
