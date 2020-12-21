<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc;


use doc\tool\Str;
use doc\traits\Build;
use doc\traits\Property;
use doc\exception\ClassNotFoundException;
use http\Exception\BadMethodCallException;

class Main
{
	use Build,Property,Create;

	/**
	 * 基类名称
	 * @var string
	 * @example $main->basicContrller = XXX::class
	 */
	public $basicController = null;

	/**
	 * 忽略的类
	 * @var array
	 * @example [ XXX:class]
	 */
	public $ignore = [];

	/**
	 * 工作目录(绝对路径)
	 * @var string
	 */
	public $workDir = '';

	/**
	 * glob函数的匹配规则
	 * @var string
	 */
	public $patternFlag = 'GLOB_ERR';

	/**
	 * 扫描模式
	 * all 扫描所有 包括目录下的目录
	 * current 仅扫描当前目录
	 * @var string
	 */
	public $pattern = 'currnet';

	/**
	 * 各个正则表达式的集合
	 * @var string[]
	 */
	public $regular = [
		  'func'   => '' //读取方法名的正则表达式
		, 'desc'   => '' //读取描述的正则表达式
		, 'method' => '' // 读取请求方法的正则表达式
		, 'params' => '' //读取参数的正则表达式
		, 'return' => '' //读取返回值的正则表达式
	];

	/**
	 * 文件的meimi (考虑到IIS好像可以把其他文件映射成PHP执行所以这写活吧)
	 * @var string
	 */
	public $memiType = 'text/x-php';

	/**
	 * 生成的文件存放路径
	 * @var string
	 */
	public $depositPath = '';

	public function __construct($workDir=''){
		$data = func_get_args();

		if(is_array($data[0])){
			foreach($data[0] as $key => $item){
				$arg  = Str::convertUnderline($key,false);

				//调用各个参数的set方法
				$this->getMethod($key,$item);
			}
		}else{
			if(empty($workDir)){
				throw new BadMethodCallException('请传入工作目录');
			}
			$this->setWorkDir($workDir);
		}
	}

	/**
	 * 检查类是否存在
	 * @param $classMap
	 * @return bool
	 */
	protected function chkClassMap($classMap){
		array_map(function($item){
			if(class_exists($item)){
				return true;
			}
			throw new ClassNotFoundException('类[' . $item . ']不存在');
		},$classMap);
		return true;
	}

	/**
	 * 给各个属性赋值
	 * @param String $funcName
	 * @param $value
	 */
	private function getMethod(String $funcName,$value){
		$property = Str::convertUnderline($funcName,false);
		$method = 'set' . ucfirst($property);
		if(method_exists($this, $method)){
			if((empty($this->$property) || is_null($this->$property)) && !$value){
				throw new \BadMethodCallException('参数['.$funcName.']不能为空');
			}
			$this->$method($value);
		}

		throw new \BadFunctionCallException('不存在的set方法[' . $method . ']');
	}
}