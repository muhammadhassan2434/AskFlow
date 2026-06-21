<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;

class AuthController extends BaseController
{
    protected string $pageRoot = 'Auth';

    public function signUp()
    {
        return $this->page('Signup');
    }

    public function login()
    {
        return $this->page('Login');
    }
}