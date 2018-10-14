<?php
class Translator
{
    private $language = null;
    private $languageDirectory = null;
    private $translate = array();
    public function __construct($language = null, $languageDirectory = null)
    {
        $this->setLanguage($language);
        $this->setLanguageDirectory($languageDirectory);
        $this->setTranslator();
    }
    public function __call($method, $args)
    {
        exit(var_export($method));
    }
    public function setLanguage($language = null)
    {
        $this->language = $language;
        if (is_null($language)) {
            $this->language = Config::app('language');
        }
        return $this;
    }
    public function setLanguageDirectory($languageDirectory = null)
    {
        $this->languageDirectory = $languageDirectory;
        if (is_null($languageDirectory)) {
            $this->languageDirectory = Config::app('languageDirectory');
        }
        return $this;
    }
    public function setTranslator()
    {
        if (is_null($this->language) || is_null($this->languageDirectory)) {
            throw new Exception("There is no language or languageDirectory defenition", 1);
        }
        if (file_exists($this->languageDirectory . $this->language . '.php')) {
            $this->translate = require $this->languageDirectory . $this->language . '.php';
            return $this;
        }
        throw new Exception("language file not found", 1);
    }
    private function find($str, $arr = null)
    {
        $array = $this->translate;
        if (!is_null($arr)) {
            $array = $arr;
        }
        return $array[$str];
    }
    public function get($str)
    {
        if (array_key_exists($str, $this->translate)) {
            return $this->find($str);
        } else if (strpos($str, '.') !== false) {
            $strArray = explode('.', $str);
            $str = $this->translate;
            $i = 0;
            while (is_array($str)) {
                $str = $this->find($strArray[$i], $str);
                $i++;
            }
        }
        return $str;
    }
}
