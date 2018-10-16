<?php

use Respect\Validation\Validator as validator;

class Validation extends validator
{
    public $rule = array();
    public $input = array();
    private $preparedRules = array();
    private $rulesDirectory = null;
    private $executedMethods = array();
    private $errors = [];
    private $isMaked = false;
    private $humanMessage = array('alnum', 'length', 'noWhitespace', 'email', 'stringVal', 'passwordConfirmation', 'avatar');
    public static function getInstance()
    {
        return new self();
    }
    public function __construct($rules = array(), $inputs = array())
    {
        $this->setRules($rules);
        $this->setInputs($inputs);
        $this->make();
    }
    public function setRulesDirectory($rulesDirectory = null)
    {
        $this->rulesDirectory = $rulesDirectory;
        if (is_null($rulesDirectory)) {
            $this->rulesDirectory = Config::app('rulesDirectory');
        }
        return $this;
    }
    public function readRules($rule_name = null, $rulesDirectory = null)
    {
        $this->setRulesDirectory($rulesDirectory);
        if (!is_null($rule_name) && file_exists($this->rulesDirectory . $rule_name . '.php')) {
            $rules = require $this->rulesDirectory . $rule_name . '.php';
            if (is_array($rules)) {
                $this->rule = $rules;
            }
            return $this;
        }
        throw new Exception("rule file not found", 1);
    }
    public function setRules($rules = null)
    {
        $this->rule = null;
        if (is_array($rules) && count($rules)) {
            $this->rule = $rules;
        } else if (is_string($rules)) {
            $this->readRules($rules);
        }
        return $this;
    }
    public function setInputs($inputs = null)
    {
        $this->input = null;
        if (is_array($inputs) && count($inputs)) {
            $this->input = arrayToObject($inputs);
        } else if (!is_null($inputs) && count($inputs)) {
            $this->input = $inputs;
        }
        return $this;
    }
    public function make()
    {
        if ($this->isMaked) {
            return $this;
        }
        if (!$this->hasInputs()) {
            $this->errors['inputs'] = UNDEFINED;
            return $this;
        }
        $this->prepareRules();
        foreach ($this->preparedRules as $key => $value) {
            $v = new parent();
            foreach ($value as $method) {
                $this->executedMethods[] = $method['method_name'];
                call_user_func_array([$v, $method['method_name']], $method['method_args']);
            }
            try
            {
                $input_value = UNDEFINED;
                if (isset($this->input->$key)) {
                    $input_value = $this->input->$key;
                }
                if (is_object($this->input->$key)) {
                    $v->assert($method['method_args']);
                } else {
                    $v->assert($input_value);
                }
            } catch (Exception $exception) {
                if (method_exists($exception, 'findMessages')) {
                    $this->errors[$key] = $this->filterMessages($exception->findMessages($this->humanMessage));
                } else {
                    $this->errors[$key] = $exception->getMessage();
                }
            }
        }
        $this->isMaked = true;
    }
    public function isPassed()
    {
        $this->resetErrors();
        $this->make();
        if (!count($this->errors)) {
            return true;
        }
        return false;
    }
    public function getMessages()
    {
        return $this->errors;
    }
    public function prepareRules()
    {
        if ($this->isPrepared()) {
            return $this;
        }
        foreach ($this->rule as $key => $value) {
            $_methods = [];
            $methods = explode('|', $value);
            $counter = 0;
            foreach ($methods as $method) {
                if (strpos($method, '[') === false) {
                    $_methods[$counter]['method_name'] = $method;
                    $_methods[$counter]['method_args'] = array();
                } else {
                    $methodArray = explode('[', $method);
                    $args = str_replace(array('[', ']'), '', end($methodArray));
                    $args = explode(':', $args);
                    for ($i = 0; $i < count($args); $i++) {
                        if ($args[$i] == 'null') {
                            $args[$i] = null;
                        }
                    }
                    $_methods[$counter]['method_name'] = $methodArray[0];
                    $_methods[$counter]['method_args'] = $args;
                }
                $counter++;
            }
            $method = null;
            $this->preparedRules[$key] = $_methods;
        }
        return $this;
    }
    public function filterMessages($array)
    {
        $messages = [];
        foreach ($array as $key => $value) {
            if ($value != "") {
                $messages[] = $value;
            }
        }
        return $messages;
    }
    public function hasRules()
    {
        if (is_array($this->rule) && count($this->rule)) {
            return true;
        }
        return false;
    }
    public function hasInputs()
    {
        if ((is_array($this->input) && count($this->input)) || is_object($this->input)) {
            return true;
        }
        return false;
    }
    private function resetErrors()
    {
        $this->errors = [];
        return $this;
    }
    public function isPrepared()
    {
        if (is_array($this->preparedRules) && count($this->preparedRules)) {
            return true;
        }
        return false;
    }
}
