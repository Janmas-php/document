<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\traits;

/**
 * 读文件往classMap里放数据
 * @package doc\traits
 */
trait Build
{
	/**
	 * 类集合
	 * @var Array
	 */
	private $classMap = [];
	/**
	 * 文件集合
	 * @var array
	 */
	private $fileMap = [];

	/**
	 * 准备文件集合，类集合
	 */
	public function begin(){
		$this->glob();
		/**
		 * 检查类集合
		 */
		$this->chkClassMap($this->classMap);
	}

	/**
	 * 把文件读入
	 */
	protected function glob($dir='')
	{
		$fileMap = glob(empty($dir)?$this->workDir:$dir,$this->patternFlag);

		/**
		 * 指定获取文件memi类型
		 */
		$memiType =  finfo_open(FILEINFO_MIME_TYPE);
		$this->classMap = array_map(function($item)use($fileMap,$memiType){
			if(is_dir($item) && $this->pattern === 'all'){
				#如果是全匹配的话就接着读
				return $this->glob($item);
			}elseif(finfo_file($memiType,$item) !== $this->memiType){
				#检查文件的memi类型所以
				return [];
			}

			$file = pathinfo($item);
			return $file['basename'];
//			return $item;
		},$fileMap);

		 array_filter($this->fileMap);
	}

	protected function buidClassMap(){
		/**
		 * 检查类集合
		 */
		$this->chkClassMap($this->classMap);
	}
}