<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc;

use doc\traits\Build;
use doc\logic\Create;
use doc\tool\{ConfigMap,Str,File};
use doc\exception\{ClassNotFoundException,FolderNotFoundException};

/**
 * Class Main
 * @package doc
 * @property ConfigMap $configMap
 */
class Main
{
	use Build;

	/**
	 * 基类名称
	 * @var string
	 * @example $main->basicContrller = XXX::class
	 */
	protected $basicController = null;

	/**
	 * 忽略的类
	 * @var array
	 * @example [ XXX:class]
	 */
	protected $ignore = [];

	/**
	 * 工作目录(绝对路径)
	 * @var string
	 */
	protected $workDir = '';

	/**
	 * 各个正则表达式的集合
     *      title  读取方法名的正则表达式
     *      url  读取api地址的正则表达式
     *      desc 读取描述的正则表达式
     *      method 读取请求方法的正则表达式
     *      params 读取参数的正则表达式
     *      return 读取返回值的正则表达式
	 * @var string[]
	 */
	protected $regular = [
		  'title'  => '/\s@title\s(.*)./'
		, 'url'    => '/\s@url\s(.*)./'
		, 'desc'   => '/\s@desc\s(.*)./'
		, 'method' => '/\s@method\s(.*)./'
		, 'params' => '/\s@param\s(.*)./'
		, 'return' => '/\s@return\s(.*)./'
	];

	/**
	 * 文件的meimi (考虑到IIS好像可以把其他文件映射成PHP然后浏览器访问执行所以这写活吧   也不知道反射类还能不能用)
	 * @var string
	 */
	protected $memiType = 'text/x-php';

	/**
	 * 生成的文件存放路径
	 * @var string
	 */
	protected $depositPath = '';

	/**
	 * 配置
	 * @var null
	 */
	protected $configMap = null;

	public function __construct($workDir='')
    {
	    $this->configMap = ConfigMap::instance();

	    $data = func_get_args();
		if(is_array($data[0])){
			foreach($data[0] as $key => $item){
				//检查并调用各个参数的set方法
				$this->getMethod($key,$item);
			}
		}else{
			if(empty($workDir)){
				throw new \Exception('请传入工作目录');
			}
			$this->setWorkDir($workDir);
		}
		$this->checkSet(); #TODO:兼容参数部分参数不传时自动传到configMap里
	}

    /**
     * 执行生成文档
     */
	public function gender()
    {
        $this->begin();
		$createInstance = new Create($this->configMap,$this->classMap);
		$createInstance->make();
		return $createInstance;
    }

	/**
	 * 检查类是否存在
	 * @param $classMap
	 * @return bool
	 */
	protected function chkClassMap( $classMap = null )
    {
        if(is_null($classMap)){
            $classMap = $this->classMap->getAll()??[];
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
        $this->configMap->offsetSet('basic_controller', $basicController);
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
       $this->configMap->offsetSet('ignore',$ignore);
    }

    /**
     * 设置工作区目录
     * @param String $workDir
     */
    public function setWorkDir(String $workDir){
        if(!is_dir(dirname($workDir))){
            throw new FolderNotFoundException('工作目录不存在');
        }
	    $this->configMap->offsetSet('work_dir',$workDir);
    }

    /**
     * 设置memi类型
     * @param $value
     */
    public function setMemiType( $memiType='text/x-php'){
	    $this->configMap->offsetSet('memi_type',$memiType);
    }

    /**
     * 设置正则表达式集合
     * @param array $regular
     */
    public function setRegular(Array $regular){
        $diff = array_diff_key($regular, $this->regular);
        //TODO:正则判断键名判断
        $this->regular = array_merge($this->regular,$regular);
        $this->configMap->offsetSet('regular',$this->regular);
    }

    /**
     * 设置文件存储路径
     * @param string $depositPath
     */
    public function setDepositPath($depositPath = ''){
        if(!is_dir($depositPath)){
            File::make($depositPath);
        }
		$this->configMap->offsetSet('deposit_path',$depositPath);

    }

    /**
     * 给各个属性赋值
     * @param String $funcName
     * @param $value
     */
    private function getMethod(String $funcName,$value = null)
    {
        $property = Str::convertUnderline($funcName,false);
        $method = 'set' . ucfirst($property);
        if(method_exists($this, $method)){
            if((empty($this->$property) || is_null($this->$property)) && !$value && $property != 'ignore'){
                throw new \BadMethodCallException('参数['.$funcName.']不能为空');
            }
            $value = $value == null?$this->$property:$value;
            $this->$method($value);
            return ;
        }

        throw new \BadFunctionCallException('不存在的set方法[' . $method . ']');
    }

	private function checkSet(){
    	$ref = new \ReflectionClass($this);
    	$propertys = $ref->getProperties(\ReflectionProperty::IS_PROTECTED);
    	foreach($propertys as $property){
    		$propertyName = $property->getName();
    		if($propertyName == 'configMap'){
    			continue;
		    }
    		if($this->configMap->offsetExists(Str::uncamelize($propertyName))){
				continue;
		    }else{
    			$this->configMap->offsetSet(Str::uncamelize($propertyName), $this->$propertyName);
		    }
	    }
	}
}