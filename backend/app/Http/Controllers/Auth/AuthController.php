<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    protected string $pageRoot = 'Auth';

    public function __construct(private AuthService $authService)
    {
    }

    public function signUp()
    {
        return $this->page('Signup');
    }

    public function storeSignup(SignupRequest $request): RedirectResponse
    {
        $this->authService->register($request->validated());

        $request->session()->regenerate();

        return redirect('/');
    }

    public function login()
    {
        return $this->page('Login');
    }

    public function storeLogin(LoginRequest $request): RedirectResponse
    {
        $this->authService->login($request->validated());

        $request->session()->regenerate();

        return redirect('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}