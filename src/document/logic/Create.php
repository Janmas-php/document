<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janmas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\logic;

use doc\exception\ClassNotFoundException;
use doc\tool\ClassMap;
use doc\tool\ConfigMap;
use doc\tool\MethodMap;

/**
 * Class Create
 * @package doc\logic
 * @property ClassMap $classMap
 * @property ConfigMap $configMap
 * @property MethodMap $methodMap
 */
class Create
{
	/**
	 * 配置集合的实例
	 * @var ConfigMap
	 */
	public $configMap;

	/**
	 * 存放类的中间集合实例
	 * @var ClassMap
	 */
	public $classMap;

	/**
	 * 存放方法的中间集合实例
	 * @var MethodMap
	 */
	public $methodMap ;

	/**
	 * 存放基类方法
	 * @var array
	 */
	public $basicControllerMethods = [];

	/**
	 * 解析类实例
	 * @var
	 */
	private $praseInstance;
	/**
	 * Create constructor.
	 * @param ConfigMap $configMap
	 * @param ClassMap $classMap
	 */
    public function __construct (ConfigMap $configMap,ClassMap $classMap)
    {
		$this->configMap = $configMap;
		$this->classMap = $classMap;
		$this->methodMap = MethodMap::instance();
		$this->praseInstance = new Parse($this->configMap, $this->methodMap);
		$this->setBasicControllerMethods();
    }

    public function make(){
	    $offset = 1;
		while($this->classMap->getLength()){
			$class = $this->classMap->offsetGet($offset);
			/**
			 * 感觉这里可以不用判断$class是否存在因为之前已经判断过了
			 */
			if(class_exists($class)){
				$ref = new \ReflectionClass($class);
				$method = $this->storageMethods($ref);
				/*if($method){
					//TODO:通知Parse类来活了准备干活
					$this->praseInstance->do($ref->getShortName().'_'.$method);
				}*/
			}
			$this->classMap->offsetUnset($offset);
			++$offset;
		}
    }

	/**
	 * 把取到的方法存放起来等待处理
	 * @param \ReflectionClass $reflectionClass
	 * @return bool
	 */
    private function storageMethods(\ReflectionClass $reflectionClass){
		$methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach($methods as $method){
			if(in_array($method->getName(),$this->basicControllerMethods)){
				continue;
			}
			$flag = $reflectionClass->getShortName().'_'.$method->getName();
			$this->methodMap->offsetSet($flag,$method);
			$this->praseInstance->do($flag);
		}

		if($this->methodMap->getLength() < 1){
			return false;
		}
    }

    public function setBasicControllerMethods(){
    	$base = $this->configMap->offsetGet('basic_controller');
    	if(!class_exists($base)){
    		throw new ClassNotFoundException('不存在的基类[' . $base . ']');
	    }

    	$ref = new \ReflectionClass($base);
    	$methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
    	foreach($methods as $method){
		    $this->basicControllerMethods[] = $method->getName();
	    }
    }
}