<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\traits;


use doc\exception\ClassNotFoundException;

trait Property
{

	/**
	 * 设置基类
	 * @param $class
	 */
	public function setBasicController(String $basicController){
		if(!class_exists($basicController)){
			throw new ClassNotFoundException('基类['.$basicController .']不存在');
		}
		$this->basicController = $basicController;
	}

	/**
	 * 设置忽略类
	 * @param array $classMap
	 */
	public function setIgnore(Array $ignore)
	{
		$this->chkClassMap($ignore);
		$this->ignore = $ignore;
	}

	/**
	 * 设置glob函数的匹配规则
	 * @param $value
	 */
	public function setPatternFlag(Int $patternFlag){
		if(!in_array($patternFlag,[GLOB_MARK,GLOB_NOSORT ,GLOB_NOCHECK ,GLOB_NOESCAPE ,GLOB_BRACE ,GLOB_ONLYDIR ,GLOB_ERR ])){
			throw new \LogicException('方法 glob 不存在'.$patternFlag.'规则');
		}
		$this->patternFlag = $patternFlag;
	}

	public function setWorkDir(String $workDir){
		if(is_dir($workDir)){
			$this->workDir = $workDir;
		}
		throw new \LogicException('工作目录不存在');
	}

	/**
	 * 设置memi类型
	 * @param $value
	 */
	public function setMemiType(String $memiType){
		$this->memiType = $memiType;
	}

	/**
	 * 设置正则表达式集合
	 * @param array $regular
	 */
	public function setRegular(Array $regular){
		$this->regular = array_merge($this->regular,$regular);
	}

	public function setDepositPath($depositPath = ''){
		if(is_dir($depositPath)){
			$this->depositPath = $depositPath;
		}

		throw new \LogicException('文件存放目录不存在');
	}
}