<?php
/*
	This is a wrapper for transitioning from version 1 to version 2. 

	Instantiate \Dice\Transitional instead of \Dice\Dice and you can use the 1.* syntax with the 2.0 branch
*/
namespace Dice;
class Transitional {
	private $dice;

	public function __construct() {
		$this->dice = new Dice;			
	}

	public function addRule($name, Rule $rule) {
		$array = [];
		foreach ($rule as $key => $value) {
			$array[$key] = $this->replaceInstances($value);
		}

		$this->dice->addRule($name, $array);
	}

	private function replaceInstances($array) {
		foreach ($array as $key => &$value) {
			if (is_array($value)) $value = $this->replaceInstances($value);
			else if ($value instanceof Instance) return ['instance' => $value->name];
		}
		return $array;		
	}

	public function getRule($name) {
		$rule = new Rule;
		$array = $this->dice->getRule($name);
		foreach ($array as $key => $value) $rule->$key = $value;
		return $rule;
	}

	public function create($component, array $args = [], $forceNewInstance = false, $share = []) {
		return $this->dice->create($component, $args, $forceNewInstance, $share);
	}
}

class Rule {
	public $shared = false;
	public $constructParams = [];
	public $substitutions = [];
	public $newInstances = [];
	public $instanceOf;
	public $call = [];
	public $inherit = true;
	public $shareInstances = [];
}

class Instance {
	public $name;
	public function __construct($instance) {
		$this->name = $instance;
	}
}