<?php
namespace Respect\Validation\Rules;

class PasswordConfirmation extends AbstractRule
{
    public function validate($input)
    {
        $slim = \Slim\Slim::getInstance();
        $posted_data = $slim->request->post();
        if(isset($posted_data['password'])) {
          return $posted_data['password'] === $input;
        }
        return false;
    }
}
