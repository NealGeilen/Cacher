<?php

namespace Cacher;

use Exception;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use function Couchbase\defaultDecoder;

class Cacher{

    const Css = "css";
    const Js = "js";

    protected $cachDirectorie = null;
    protected $name = null;
    protected $Files = [
        self::Css => [],
        self::Js => []
    ];
    /**
     * @var JS
     */
    private $minifyJS;
    /**
     * @var CSS
     */
    private $minifyCSS;

    private $func = null;


    /**
     * Cacher constructor.
     */
    public function __construct()
    {
        $this->minifyJS = new JS();
        $this->minifyCSS = new CSS();
    }

    /**
     * @param array $aFiles
     * @return $this
     * @throws Exception
     */
    public function setFiles(array $aFiles){
        if (!isset($aFiles[self::Css]) || !isset($aFiles[self::Js])){
            throw new Exception("Js & Css object not defined", 500);
        }
        $this->Files[self::Js] = $aFiles[self::Js];
        $this->Files[self::Js] = $aFiles[self::Css];
        return $this;
    }

    /**
     * @param string $type
     * @param string $source
     * @return $this
     */
    public function add(string $type,string $source){
        $this->Files[$type][] = $source;
        return $this;
    }

    /**
     * @return string
     */
    protected function getCachDirectorie()
    {
        $sDir = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $this->cachDirectorie;
        if (!is_dir($sDir)){
            mkdir($sDir,0777,true);
        }
        return $sDir;
    }

    /**
     * @param $cachDirectorie
     * @return $this
     */
    public function setCachDirectory($cachDirectorie)
    {
        $this->cachDirectorie = $cachDirectorie;
        return $this;
    }



    protected function build(){
        foreach ($this->Files[self::Css] as $file){
            $this->minifyCSS->add($file);
        }
        foreach ($this->Files[self::Js] as $file){
            $this->minifyJS->add($file);
        }
    }

    /**
     * @param callable $function
     * @return $this
     */
    public function callback(callable $function){
        if (is_callable($function)){
            $this->func = $function;
        }
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function Minify(){
        if (!is_null($this->func)){
            call_user_func($this->func, $this);
        }
        /**
         * Check if directory is defined
         */
        if (!is_null($this->cachDirectorie)){
            $this->build();
        } else {
            throw new Exception("Cach directory is not given", 500);
        }


        if (!empty($this->Files[self::Js])) {
            $this->minifyJS->minify($this->getJsFile());
        }

        if (!empty($this->Files[self::Css])) {
            $this->minifyCSS->minify($this->getCssFile());
        }
        return $this;

    }

    /**
     * @return array
     * @throws Exception
     */
    public function getMinifyedFiles(){
        $aReturnData = [];

        if (is_file($this->getJsFile())){
            $aReturnData[self::Js] = $this->getJsFile(false);
        }

        if (is_file($this->getCssFile())){
            $aReturnData[self::Css] = $this->getCssFile(false);
        }

        return $aReturnData;
    }

    /**
     * @param bool $relative
     * @return string
     */
    public function getJsFile($relative = true):string
    {
        return ($relative) ? $this->getCachDirectorie() . DIRECTORY_SEPARATOR .$this->getName(). ".min.js" : "/" . $this->cachDirectorie . "/". $this->getName(). ".min.js";
    }

    /**
     * @param bool $relative
     * @return string
     */
     public function getCssFile($relative = true):string
     {
         return ($relative) ? $this->getCachDirectorie() . DIRECTORY_SEPARATOR .$this->getName(). ".min.css" : "/" . $this->cachDirectorie . "/". $this->getName(). ".min.css";
     }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


}