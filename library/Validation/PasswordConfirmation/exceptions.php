<?php
namespace Respect\Validation\Exceptions;

class PasswordConfirmationException extends ValidationException
{
    public static $defaultTemplates = array(
        self::MODE_DEFAULT => array(
            self::STANDARD => 'passwords missed match',
        ),
        self::MODE_NEGATIVE => array(
            self::STANDARD => 'passwords missed match',
        )
    );
}
