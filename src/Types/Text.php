<?php


namespace RawInput\Types;

/**
 * Class      Text
 * @package RawInput\Types
 */
class Text extends BaseType
{
    protected static function validateAndSanitize($value)
    {
        $sanitizedValue = htmlspecialchars(trim($value));
        if (mb_strlen($sanitizedValue) < 5) {
            throw new ValidateException('Wrong text format');
        }
        return $sanitizedValue;
    }
}
