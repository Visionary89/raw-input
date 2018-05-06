<?php

use RawInput\Types\PhoneNumberCollection;

class PhoneNumberCollectionTest extends PHPUnit\Framework\TestCase
{

    public function rightNumbersProvider()
    {
        return [
            ['+79031234567', ['9031234567']],
            ['+7(903)1234567', ['9031234567']],
            ['+7(903)123-45-67', ['9031234567']],
            ['call me after 11 am: +7(903)123-45-67              ', ['9031234567']],
            ['+7(903)123 45 99, 89031234588', ['9031234599', '9031234588']],
            ['89031234567', ['9031234567']],
            ['9031234567', ['9031234567']],
            ['1234567', ['4951234567']],
        ];
    }

    /**
     * @dataProvider rightNumbersProvider
     */
    public function testValidateAndSanitizeSuccess($value, $sanitizedValue)
    {
        $phoneNumber = new PhoneNumberCollection($value);
        $this->assertEquals($phoneNumber->getSanitizedValue(), $sanitizedValue);
    }


    public function wrongNumbersProvider()
    {
        return [
            ['903123456'],
            ['+37771234567'],
            ['+7(903)12345+67'],
            ['    ds  +7   (903)   123----45------67   asdfadf      '],
            ['+7(903)123 45 6789031234567'],
            ['12344'],
        ];
    }

    /**
     * @dataProvider wrongNumbersProvider
     * @expectedException \RawInput\Types\ValidateException
     */
    public function testValidateAndSanitizeError($value)
    {
        new PhoneNumberCollection($value);
    }
}
