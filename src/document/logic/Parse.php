<?php
//+-----------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+-----------------------------------------------------------
//| Author:Janas <janmas@126.com>
//+-----------------------------------------------------------
//| 
//+-----------------------------------------------------------

namespace doc\logic;


use doc\tool\ConfigMap;
use doc\tool\File;
use doc\tool\MethodMap;

/**
 * Class Parse
 * @package doc\logic
 * @property ConfigMap $configMap
 * @property MethodMap $methodMap
 */
class Parse
{
	public $configMap;
	public $methodMap;

	public function __construct(ConfigMap $configMap ,MethodMap $methodMap ){
		$this->configMap = $configMap;
		$this->methodMap = $methodMap;
	}

	/**
	 * 开始处理方法
	 * @param $shortName
	 */
	public function do($shortName){
		/**
		 * @var \ReflectionMethod $method
		 */
		$refMethod = $this->methodMap->offsetGet($shortName);
		if(!$refMethod){
			return ;
		}
		$docComment = $refMethod->getDocComment();
		if(!$docComment){
            $this->methodMap->offsetUnset($shortName);
		    return ;
        }
		$regular = ($this->configMap->offsetGet('regular'));
		$content = [];
		foreach($regular as $key=>$value){
			if($key != 'params'){
				$content[$key] = $this->getByMatches($value,$docComment);
			}else{
				$methodName = 'get'.ucfirst($key);
				$content[$key] = $this->$methodName($value,$docComment);
			}
		}
		array_filter($content);
		if(!empty($content)){
			$file = $this->checkFile($refMethod,$shortName);
			$this->buildFile($content,$refMethod,$file,$shortName);
			return ;
		}
	}

	/**
	 * 单匹配
	 * @param $parttern
	 * @param $docComment
	 * @return mixed|string
	 */
	private function getByMatches($parttern,$docComment){
		if(empty($parttern)){
			return '';
		}
		preg_match($parttern,$docComment,$matches);
		return array_pop($matches);
	}

	/**
	 * 多匹配
	 * @param $parttern
	 * @param $docComment
	 * @return mixed|string
	 */
	private function getParams($parttern,$docComment){
		if(empty($parttern)){
			return '';
		}
		preg_match_all($parttern,$docComment,$matches);
		return array_pop($matches);
	}

	/**
	 * 检查文件
	 * @param $refMethod
	 * @param $shortName
	 * @return string
	 */
	private function checkFile($refMethod,$shortName){
		if(!is_file($this->configMap->offsetGet('deposit_path').'/'.$shortName.'.md')){
			File::make($this->configMap->offsetGet('deposit_path').'/'.$shortName.'.md',true);
			return $this->configMap->offsetGet('deposit_path').'/'.$shortName.'.md';
		}
	}

	/**
	 * 生成文件
	 * @param $content
	 * @param $refMethod
	 * @param $file
	 * @param $shortName
	 */
	private function buildFile($content,$refMethod,$file,$shortName){
		//存文件
		$file = '';
		extract($content);
		if(!isset($url) ){
			$url = $refMethod->class.'/'.$refMethod->getName();
		}
		$file .= <<<EOF
### $title

##### 简要描述
- `$desc`

##### 请求地址
- `$url` \r\n

##### 请求方式
- `$method `

EOF;
		if(!empty($params)){
			$file .= "##### 参数\r\n| 参数名  | 类型   | 描述 |\r\n| :------ | :----- | :------ |\r\n";
			foreach($params as $param){
				$param = explode(' ',$param);
				$param[1] = str_replace('$','',$param[1]);
				$file .= "| $param[1]  | $param[0]    | $param[2]|\r\n";
			}
		}

		if(isset($return)){
			$file .= "### 返回示例\r\n```\r\n{$return}\r\n```";
		}

		$file .= "---\r\n";

		if(file_put_contents($this->configMap->offsetGet('deposit_path').'/'.$shortName.'.md', $file)){
			$this->methodMap->offsetUnset($shortName);
		}
	}
}