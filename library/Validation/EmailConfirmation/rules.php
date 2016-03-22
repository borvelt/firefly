<?php
namespace Respect\Validation\Rules;

class EmailConfirmation extends AbstractRule
{
    public function validate($input)
    {
        $slim = \Slim\Slim::getInstance();
        $posted_data = $slim->request->post();
        if(isset($posted_data['email'])) {
          return $posted_data['email'] === $input;
        }
        return false;
    }
}
