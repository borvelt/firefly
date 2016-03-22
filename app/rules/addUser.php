<?php

return
    [
        'name_family'=>'StringType|length[3:50]',
        'email'=>'email',
        'password'=>'notEmpty|length[4:null]',
        'confirmation_password'=>'passwordConfirmation',
    ];
