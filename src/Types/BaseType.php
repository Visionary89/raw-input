<?php


namespace RawInput\Types;

/**
 * Class      BaseType
 * @package RawInput\Types
 */
abstract class BaseType implements TypeInterface
{
    protected $value;
    protected $sanitizedValue;

    public function __construct($value)
    {
        $this->value = $value;
        $this->sanitizedValue = static::validateAndSanitize($this->value);
    }

    abstract protected static function validateAndSanitize($value);

    /**
     * @return mixed
     */
    public function getSanitizedValue()
    {
        return $this->sanitizedValue;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
