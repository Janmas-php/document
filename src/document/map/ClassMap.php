<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janmas <janmas@126.com>
//+-----------------------------------------------------------
//| 存放类集合
//+-----------------------------------------------------------

namespace doc\map;


class ClassMap implements \ArrayAccess
{
    private $classMap;

    public static $instance;

    public static function instance(){
        if(!is_null(self::$instance) && self::$instance instanceof self){
            return self::$instance;
        }
        self::$instance = new self;
        return self::$instance;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists ( $offset ):bool
    {
        return isset($this->classMap[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet ( $offset ):String
    {
        if($this->offsetExists($offset)){
            return $this->classMap[$offset];
        }
        throw new \LogicException('错误的偏移量');
    }

    /**
     * @inheritDoc
     */
    public function offsetSet ( $offset , $value )
    {
        if(!$this->offsetExists($offset)){
            $this->classMap[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset ( $offset )
    {
        unset($this->classMap[$offset]);
    }

    public function getAll(){
        return $this->classMap;
    }

    public function getLength():Int
    {
		return $this->classMap?count($this->classMap):0;
    }
}