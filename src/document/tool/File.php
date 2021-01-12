<?php
#+-----------------------------------------------------
#| 人生就像苦难的旅途，苦难在心，欢乐自知
#+-----------------------------------------------------
#| Janmas <janmas@126.com>
#+-----------------------------------------------------
#|
#+-----------------------------------------------------

namespace doc\tool;


class File
{
    /**
     * 执行生成文件夹
     * @param $dir
     * @return bool
     */
    protected static function gender(String $dir){
        if(!is_dir($dir)){
            mkdir($dir,true,0777);//鸡肋的递归  智障般的操作 。。这个永远也不会递归。。
	        if(is_dir($dir)){
                return true;
            }
            self::gender($dir);
        }else{
            return true;
        }
    }

    /**
     * 对外开放的口子
     * @param String $dir 目录路径或者文件路径
     * @param false $isFile
     * @return bool
     */
    public static function make(String $dir,$isFile=false){
        if($isFile){
            if(self::gender(dirname($dir)) && touch($dir)){
                return true;
            }else{
                return false;
            }
        }else{
            return self::gender($dir);
        }
    }

}