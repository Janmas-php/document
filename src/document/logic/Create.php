<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janmas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\logic;


class Create
{

    /**
     * 正则集合
     * @var array
     */
    public $pattern = [];

    /**
     * 文件存储路径
     * @var String
     */
    public $depositPath = '';

    public function __construct (Array $pattern,String $depositPath,)
    {
        $this->pattern = $pattern;
        $this->depositPath = $depositPath;

    }
}