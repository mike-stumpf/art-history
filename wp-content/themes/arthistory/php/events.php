<?php namespace artHistory\Data;

//todo, documentation

class Event {
    public $foo;

    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    public function __toString()
    {
        return $this->foo;
    }
}

$class = new Event('Hello');
echo $class;