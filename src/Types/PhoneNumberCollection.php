<?php


namespace RawInput\Types;

/**
 * Class      PhoneNumberCollection
 * @package RawInput\Types
 */
class PhoneNumberCollection extends BaseType
{

    protected static function validateAndSanitize($value)
    {
        $matches = [];
        if (!preg_match_all(
            '/(?:\D|^)(?:\+7|8)?((?:\(?\d{3}\)?)?\d{3}[\s-]?\d{2}[\s-]?\d{2})(?:\D|$)/',
            trim($value),
            $matches
        )) {
            throw new ValidateException('Wrong phone number format');
        }
        $sanitizedValues = [];
        foreach ($matches[1] as $matched) {
            $sanitizedValue = str_replace([' ', "\t", '-', '(', ')'], '', $matched);
            if (strlen($sanitizedValue) === 7) {
                $sanitizedValue = '495' . $sanitizedValue;
            }

            $sanitizedValues[] = $sanitizedValue;
        }
        return $sanitizedValues;
    }
}
