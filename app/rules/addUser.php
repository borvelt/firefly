<?php

return
    [
    'name_family' => 'StringType|length[3:50]',
    'email' => 'email',
    'password' => 'notEmpty|length[4:null]',
    'confirmation_password' => 'passwordConfirmation',
    'avatar_attachment' => 'Avatar[avatar_attachment:image/png:image/jpeg:image/jpg:100000:optional]', //optional should be last parameter. and first parameter is input_name (handle it in library/validation/Avatar/rules.php)
];
