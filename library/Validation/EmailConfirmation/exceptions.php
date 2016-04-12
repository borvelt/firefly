<?php
namespace Respect\Validation\Exceptions;

class EmailConfirmationException extends ValidationException
{
    public static $defaultTemplates = array(
        self::MODE_DEFAULT => array(
            self::STANDARD => 'emails missed match',
        ),
        self::MODE_NEGATIVE => array(
            self::STANDARD => 'emails missed match',
        )
    );
}
