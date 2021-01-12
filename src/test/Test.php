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
	'basic_controller' => \test\classes\Basic::class, //基类
	'ignore' => [
		\test\classes\A::class
//		'./classes/C.php'
	], //需要忽略的类
	'work_dir' => dirname(__FILE__) . '/classes/*.php',//工作目录
	'deposit_path' => './a',//保存的路径
];

$main = new Main($arr);
$create = $main->gender();

