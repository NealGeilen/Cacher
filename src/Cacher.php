<?php

namespace Cacher;

use Exception;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

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


    /**
     * Cacher constructor.
     */
    public function __construct()
    {
        $this->minifyJS = new JS();
        $this->minifyCSS = new CSS();
    }


    /**
     * @param string $type
     * @param string $source
     * @return $this
     */
    public function add($type, $source){
        $this->Files[$type][] = $source;
        return $this;
    }

    /**
     * @return null|string
     */
    protected function getCachDirectorie()
    {
        $sDir = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $this->cachDirectorie;
        if (!is_dir($sDir)){
            mkdir($sDir);
        }
        return $sDir;
    }

    /**
     * @param string $cachDirectorie
     */
    public function setCachDirectorie($cachDirectorie)
    {
        $this->cachDirectorie = $cachDirectorie;
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
     * @return array
     * @throws Exception
     */
    public function getMinifyedFiles(){
        /**
         * Check if directorie is defined
         */
        if (!is_null($this->cachDirectorie)){
            $this->build();
        } else {
            throw new Exception("Cach directorie is not given", 500);
        }
        /**
         * File locations
         */
        $JsFile = $this->getCachDirectorie() . DIRECTORY_SEPARATOR .$this->getName(). ".min.js";
        $CssFile = $this->getCachDirectorie() .DIRECTORY_SEPARATOR .$this->getName().".min.css";
        /**
         * Check Js File
         */
        if (is_file($JsFile)){
            if (filemtime($JsFile) > time() + 86400){
                $this->minifyJS->minify($JsFile);
            }
        } else {
            $this->minifyJS->minify($JsFile);
        }

        /**
         * Chek Css File
         */
        if (is_file($CssFile)){
            if (filemtime($CssFile) > time() + 86400){
                $this->minifyCSS->minify($CssFile);
            }
        } else {
            $this->minifyCSS->minify($CssFile);
        }

        return [
            self::Css => "/" . $this->cachDirectorie . "/". $this->getName(). ".min.css",
            self::Js => "/" . $this->cachDirectorie . "/". $this->getName(). ".min.js",
        ];
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
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}