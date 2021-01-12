<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\map;


class ConfigMap implements \ArrayAccess
{
	public $config = [];

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
		return isset($this->config[$offset]);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet( $offset )
	{
		if($this->offsetExists($offset)){
			return $this->config[$offset];
		}

		throw new \LogicException('不存在的配置信息[' . $offset . ']');
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet( $offset, $value )
	{
		$this->config[$offset] = $value;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset( $offset )
	{
		unset($this->config[$offset]);
	}

	public function getAll(){
		return $this->config;
	}
}