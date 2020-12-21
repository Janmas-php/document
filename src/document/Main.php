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
use doc\tool\File;
use doc\traits\Build;
use doc\exception\ClassNotFoundException;
use doc\exception\FolderNotFoundException;


class Main
{
	use Build;

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
	 * glob函数的匹配规则 TODO:多余参数
	 * @var string
	 */
	public $patternFlag = GLOB_ERR;

	/**
	 * 扫描模式 TODO:多余参数
	 * all 扫描所有 包括目录下的目录
	 * current 仅扫描当前目录
	 * @var string
	 */
	public $pattern = 'currnet';

	/**
	 * 各个正则表达式的集合
     *      func 读取方法名的正则表达式
     *      desc 读取描述的正则表达式
     *      method 读取请求方法的正则表达式
     *      params 读取参数的正则表达式
     *      return 读取返回值的正则表达式
	 * @var string[]
	 */
	public $regular = [
		  'func'   => ''
		, 'desc'   => ''
		, 'method' => ''
		, 'params' => ''
		, 'return' => ''
	];

	/**
	 * 文件的meimi (考虑到IIS好像可以把其他文件映射成PHP然后浏览器访问执行所以这写活吧   也不知道反射类还能不能用)
	 * @var string
	 */
	public $memiType = 'text/x-php';

	/**
	 * 生成的文件存放路径
	 * @var string
	 */
	public $depositPath = '';

	public function __construct($workDir='')
    {
		$data = func_get_args();
		if(is_array($data[0])){
			foreach($data[0] as $key => $item){
				//检查并调用各个参数的set方法
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
     * 执行生成文档
     */
	public function gender()
    {
        $this->begin();

    }

	/**
	 * 检查类是否存在
	 * @param $classMap
	 * @return bool
	 */
	protected function chkClassMap( $classMap = null )
    {
        if(is_null($classMap)){
            $classMap = $this->classMap->getAll();
        }
        foreach($classMap as $class) {
            if ( !class_exists($class) ) {
                throw new ClassNotFoundException('类[' . $class . ']不存在');
            }
        }
		return true;
	}
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
    public function setIgnore($ignore=[])
    {
       if(!is_array($ignore)){
            throw new \LogicException('参数类型错误[ignore]');
        }
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

    /**
     * 设置工作区目录
     * @param String $workDir
     */
    public function setWorkDir(String $workDir){
        if(!is_dir(dirname($workDir))){
            throw new FolderNotFoundException('工作目录不存在');
        }
        $this->workDir = $workDir;
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
        $diff = array_diff_key($regular, $this->regular);
        //TODO:正则判断键名判断
        $this->regular = array_merge($this->regular,$regular);
    }

    /**
     * 设置文件存储路径
     * @param string $depositPath
     */
    public function setDepositPath($depositPath = ''){
        if(!is_dir($depositPath)){
            File::make($depositPath);
//            throw new FolderNotFoundException('文件存放目录不存在');
        }

        $this->depositPath = $depositPath;

    }

    public function setPattern($pattern = ''){
        if(in_array($pattern,['current','all'])){
            $this->pattern = $pattern;
        }else{
            $this->pattern = 'current';
        }
    }

    /**
     * 给各个属性赋值
     * @param String $funcName
     * @param $value
     */
    private function getMethod(String $funcName,$value)
    {
        $property = Str::convertUnderline($funcName,false);
        $method = 'set' . ucfirst($property);
        if(method_exists($this, $method)){
            if((empty($this->$property) || is_null($this->$property)) && !$value && $property != 'ignore'){
                throw new \BadMethodCallException('参数['.$funcName.']不能为空');
            }
            $this->$method($value);
            return ;
        }

        throw new \BadFunctionCallException('不存在的set方法[' . $method . ']');
    }

}