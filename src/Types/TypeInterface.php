<?php


namespace RawInput\Types;

/**
 * Interface: TypeInterface
 * @package RawInput\Types
 */
interface TypeInterface
{
    public function getSanitizedValue();
    public function getValue();
}
