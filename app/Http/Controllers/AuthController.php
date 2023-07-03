<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginActionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends CoreController
{
    public function __construct() {

    }

    public function login() {
        return view('auths.login');
    }
    public function denied(){
        return view('auths.denied');
    }

    public function loginAction(LoginActionRequest $request) {
        // dd($request->all());
        $remember = $request->has('remember_me') ? true : false;
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'is_active' => 1])) {
            //sync user and customer
            if($remember == true) {
                Cookie::queue('_remember_log', Auth::user()->name, 3600);
                Cookie::queue('username', $request->username, 3600);
                Cookie::queue('password', $request->password, 3600);
            }
            return redirect()->route('list-partner')->with(['success' => "Đăng nhập thành công"]);
        }
        return redirect()->back()->withErrors(['messages' => 'Tên đăng nhập hoặc mật khẩu không đúng']);
    }

    public function logout(Request $request) {
        $request->session()->flush();
        Auth::logout();
        return redirect()->route('logout');
    }
}
