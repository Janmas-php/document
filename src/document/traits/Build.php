<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 读文件往classMap里放数据
//+-----------------------------------------------------------

namespace doc\traits;

use doc\exception\ClassNotFoundException;
use doc\map\{ClassMap, ConfigMap};

/**
 * Trait Build
 * @package doc\traits
 * @property ClassMap $classMap;
 * @property ConfigMap $configMap;
 */
trait Build
{
	/**
	 * 类集合
	 * @var Array
	 */
	private $classMap = null;

	/**
	 * 文件集合 暂时没有用到
	 * @var array
	 */
	private $ignoreFileMap = [];

	/**
	 * 准备文件classMap
	 */
	public function begin()
	{
		$this->classMap = ClassMap::instance();
		if ( !empty($this->configMap->offsetGet('ignore')) ){
			$this->ignoreFile();
		}
		/**
		 * 读文件存类
		 */
		$this->glob();
		/**
		 * 检查类集合
		 */
		$this->chkClassMap();
	}

	/**
	 * 把文件读入classMap
	 * @param string $dir
	 */
	protected function glob( $dir = '' )
	{
		$fileMap = glob(empty($dir) ? $this->configMap->offsetGet('work_dir') : $dir);
		$basicClass = $this->configMap->offsetGet('basic_controller');
		$basicObject = new $basicClass;
		/**
		 * 指定获取文件memi类型
		 */
		$memiType = finfo_open(FILEINFO_MIME_TYPE);
		$offset = 1 ;
		foreach ($fileMap as $file){
			if ( finfo_file($memiType, $file) !== $this->configMap->offsetGet('memi_type') ){
				continue;
			}

			$file = str_replace('/', '\\', $file);

			if ( in_array($file, $this->ignoreFileMap) ){
				continue;
			}

			$filename = pathinfo($file)['filename'];
			$class = $this->getNamespace($file, $filename);
			if ( new $class == $basicObject || !class_exists($class) ){
				continue;
			}

			$this->classMap->offsetSet($offset, '\\' . $class);
			++$offset;
		}
	}

	/**
	 * 暂时用不到
	 */
	protected function buidClassMap()
	{
		$this->chkClassMap($this->classMap);
	}

	/**
	 * 获取当前文件的命名空间
	 * @param $file
	 * @return string
	 */
	protected function getNamespace( $file, $className )
	{

		$namespacePattern = '/(.*)?namespace\s(.*)?;/';

		$namespace = '';
		$stream = fopen($file, 'r');
		while (empty($namespace)){
			$content = fgets($stream);//逐行读取（感觉一次读多行更好反正namespace都在最前面）

			if ( preg_match($namespacePattern, $content, $namespace) ){
				$namespace = array_pop($namespace);
			} else if ( feof($stream) ){
				throw new ClassNotFoundException('文件' . $file . '没有命名空间');
			} else{
				continue;
			}
		}
		fclose($stream);
		$class = $namespace . '\\' . $className;
		return $class;
	}

	/**
	 * 把忽略文件的类读成文件
	 */
	protected function ignoreFile()
	{
		foreach ($this->configMap->offsetGet('ignore') as $class){
			$reflector = new \ReflectionClass($class);
			$fn = $reflector->getFileName();
			$this->ignoreFileMap[] = ($fn);
		}
	}
}