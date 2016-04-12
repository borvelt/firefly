<?php

class UsersController
{

    public function login ($slim, $validator, $translator)
    {
        $posted_data = $slim->request->post();
        $validator->setRules("loginUser")->setInputs($posted_data);
        if(!$validator->isPassed())
        {
            $slim->responseBody = $validator->getMessages();
            $slim->responseMessage = $translator->get('user_login_inputs_notValid');
            $slim->responseCode = 400;
            return ;
        }
        $validateCredential = Authenticate::validateCredential($posted_data['email'], $posted_data['password']);
        if (!$validateCredential)
        {
            $slim->responseMessage = $translator->get('user_login_miss_match');
            $slim->responseCode = 404;
            return ;
        }
        if($user = User::whereEmail($posted_data['email'])->first())
        {
            $slim->user = $user;
        }

        // $old_authentication = Authentication::whereUser($user->id)->whereExpired(false)->first();
        // if($old_authentication)
        // {
        //     $old_authentication->expired = true;
        //     $old_authentication->save();
        // }

        $api_key = Authenticate::generateNewToken();
        $authentication = new Authentication();
        $authentication->api_key = $api_key;
        $authentication->user = $user->id;
        $authentication->save();
        Authorize::token();
        $slim->responseMessage = $translator->get('userـloginـsuccess');
    }

    public function register ($slim, $validator, $translator)
    {
        $posted_data = $slim->request->post();
        $validator->setRules('addUser')->setInputs($posted_data);
        if(!$validator->isPassed())
        {
            $slim->responseBody = $validator->getMessages();
            $slim->responseMessage = $translator->get('user_create_inputs_notValid');
            $slim->responseCode = 400;
            return ;
        }
        massAssignment(new User(), $posted_data, $user);
        try
        {
            if ($user->save())
            {
                $slim->responseBody = compact("user");
                $slim->responseMessage = $translator->get('userـcreateـok');
            }
        }
        catch (Exception $e)
        {
            $slim->responseMessage = $e->getMessage();
            $slim->responseCode = 503;
        }
    }

}
