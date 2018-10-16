<?php

namespace Respect\Validation\Exceptions;

class AvatarException extends ValidationException
{
    public static $defaultTemplates = array(
        self::MODE_DEFAULT => array(
            self::STANDARD => 'File data need to send by policies',
        ),
        self::MODE_NEGATIVE => array(
            self::STANDARD => 'File data not need to send by policies',
        ),
    );
}
