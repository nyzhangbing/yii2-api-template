<?php

namespace app\enums;

use ReflectionClass;
use stdClass;
use UnexpectedValueException;

class Enum
{
    const __default = null;
    private $value;
    private $strict;
    private static $constants = array();

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    final public static function className()
    {
        return get_called_class();
    }

    /**
     * Creates new enum object. If child class overrides __construct(),
     * it is required to call parent::__construct() in order for this
     * class to work as expected.
     *
     * @param mixed $initial_value Any value that is exists in defined constants
     * @param bool $strict If set to true, type and value must be equal
     * @throws UnexpectedValueException If value is not valid enum value
     */
    final public function __construct($initial_value = self::__default, $strict = true)
    {
        $class = get_class($this);
        if (!array_key_exists($class, self::$constants)) {
            self::populateConstants();
        }
        if ($initial_value === null) {
            $initial_value = self::$constants[$class]["__default"];
        }
        $temp = self::$constants[$class];
        if (!in_array($initial_value, $temp, $strict)) {
            throw new UnexpectedValueException("Value is not in enum " . $class);
        }
        $this->value = $initial_value;
        $this->strict = $strict;
    }

    /**
     * Returns list of all defined constants in enum class.
     * Constants value are enum values.
     *
     * @param bool $include_default If true, default value is included into return
     * @return array Array with constant values
     */
    final public function getConstList($include_default = false)
    {
        $class = get_class($this);
        if (!array_key_exists($class, self::$constants)) {
            self::populateConstants();
        }
        $items = self::$constants[$class];
        if (!$include_default)
            unset($items['__default']);
        return $items;
    }

    final public function translate($value)
    {
        $items = array_flip($this->getConstList(false));
        $raw = isset($items[$value]) ? $items[$value] : '';
        return isset($this->getDisplayNames()[$value]) ? $this->getDisplayNames()[$value] : $raw;
    }

    final public function getKeyValuePairs($includeDefault = false)
    {
        $class = get_class($this);
        if (!array_key_exists($class, self::$constants)) {
            self::populateConstants();
        }
        $items = self::$constants[$class];
        if (!$includeDefault)
            unset($items['__default']);
        $items = array_flip($items);
        $result = [];
        array_walk($items, function ($value, $key) use (&$result) {
            $obj = new stdClass();
            $obj->key = $key;
            $obj->value = self::translate($key);
            array_push($result, $obj);
        });
        return $result;
    }

    private function populateConstants()
    {
        $class = get_class($this);
        $r = new ReflectionClass($class);
        $constants = $r->getConstants();
        self::$constants = array(
            $class => $constants
        );
    }

    /**
     * Returns string representation of an enum. Defaults to
     * value casted to string.
     *
     * @return string String representation of this enum's value
     */
    final public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Checks if two enums are equal. Only value is checked, not class type also.
     * If enum was created with $strict = true, then strict comparison applies
     * here also.
     *
     * @param $object
     * @return bool True if enums are equal
     */
    final public function equals($object)
    {
        if (!($object instanceof Enum)) {
            return false;
        }
        return $this->strict ? ($this->value === $object->value)
            : ($this->value == $object->value);
    }

    protected function getDisplayNames()
    {
        return [];
    }
}