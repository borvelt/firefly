<?php
class Seeder {
    private $name = null;
    private $dependencies = [];
    private $seedsDirectory = null;
    private $data = null;
    private $orderby = [];
    public function __construct($name = null, $dependencies = [], $seedsDirectory = null) {
        $this->setName($name);
        $this->setDependencies($dependencies);
        $this->setSeedsDirectory($seedsDirectory);
    }
    public function setSeedsDirectory ($seedsDirectory = null) {
        $this->seedsDirectory = $seedsDirectory;
        if(is_null($seedsDirectory)) {
            $this->seedsDirectory = Config::app('seedsDirectory');
        }
        return $this;
    }
    public function setName($name = null) {
        if(!is_null($name)) {
            $this->name = $name;
        }
        return $this;
    }
    public function setDependencies($dependencies = []) {
        if(!is_null($dependencies) && is_array($dependencies) && count($dependencies)) {
            $this->dependencies = $dependencies;
        }
        return $this;
    }
    public function seeding() {
        if(file_exists($this->seedsDirectory.$this->name.'.php')) {
            $this->data[$this->name] = require $this->seedsDirectory.$this->name.'.php';
            $this->checkDependencies();
            return $this;
        }
        throw new Exception("Seed file not found", 1);
        return $this;
    }
    public function checkDependencies() {
        if(is_null($this->data)) {
            return $this;
        }
        $dependencies = $this->data[$this->name]['dependencies'];
        array_push($this->orderby,$this->name);
        if($dependencies && is_array($dependencies) && count($dependencies)) {
            foreach ($dependencies as $depend) {
                $this->setName($depend)->seeding();
            }
        }
        return $this;
    }
    public function seed ($data = null) {
        if(is_null($data)) {
            return $this;
        }
        try
        {
            $exec_func = call_user_func_array($data['seeds'], func_get_args());
            foreach ($exec_func as $seeds) {
                $obj = new $data['model']();
                foreach ($seeds as $key => $value) {
                    $obj->$key = $value;
                }
                $obj->save();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage()." - LINE: ".$e->getLine()." - FILE: ".$e->getFile(), 1);
        }
    }
    public function start () {
        $this->seeding();
        if(is_null($this->orderby)) {
            return $this;
        }
        $this->orderby = array_unique($this->orderby);
        $this->orderby = array_reverse($this->orderby);
        foreach($this->orderby as $model) {
            $this->seed($this->data[$model]);
        }
        return $this;
    }
}
