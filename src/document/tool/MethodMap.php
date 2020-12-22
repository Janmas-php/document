<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\tool;


class MethodMap implements \ArrayAccess
{
	public $methodMap = [];

	public static $instance;

	public static function instance(){
		if(self::$instance instanceof self){
			return self::$instance;
		}

		self::$instance = new self;
		return self::$instance;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists( $offset )
	{
		return isset($this->methodMap[$offset]);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet( $offset )
	{
		if($this->offsetExists($offset)){
			return $this->methodMap[$offset];
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet( $offset, $value )
	{
		$this->methodMap[$offset] = $value;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset( $offset )
	{
		unset($this->methodMap[$offset]);
	}

	public function getAll(){
		return $this->methodMap;
	}

	public function getLength(){
		return $this->methodMap?count($this->methodMap):0;
	}
}