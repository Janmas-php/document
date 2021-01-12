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
use doc\map\{ClassMap,ConfigMap,MethodMap};
use doc\tool\Str;

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
			$ref = new \ReflectionClass($this->classMap->offsetGet($offset));
			$this->storageMethods($ref);
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
			$flag = Str::uncamelize($reflectionClass->getShortName()) .'_'.$method->getName();
			$this->methodMap->offsetSet($flag,$method);
			$this->praseInstance->do($flag);
		}

		return true;
    }

    public function setBasicControllerMethods(){
    	if(!$this->configMap->offsetExists('basic_controller')){
    		return ;
	    }
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