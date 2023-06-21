<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $usernameOrEmail = $request->username;
        $user = User::select("user_types.type", "user_type", "first_name", "surname", "email")
                ->where(function ($query) use ($usernameOrEmail){
                    return $query->where('username', $usernameOrEmail)->orWhere('email', $usernameOrEmail);
                })
                ->leftJoin("user_types", "users.user_type", "user_types.id")
                ->first();

        if (!$user)
            return response([
                'error' => "e-mail/username ou senha errados."
            ], 500);

        $success = Auth::attempt(['email' => $user->email, 'password' => $request->password]);
        if (!$success)
            return response([
                'error' => "e-mail/username ou senha errados."
            ], 500);

        if ($user->user_type == 1) $redirect = route('adm.menu');
        else if ($user->user_type == 2) $redirect = route('tutor.turmas');
        else if ($user->user_type == 4) $redirect = route('aluno.inicio');

        return response([
            'type' => $user->type,
            'user_type' => $user->user_type,
            'first_name'=> $user->first_name,
            'surname' => $user->surname,
            'redirect' => $redirect,
        ]);
    }

    public function logout(){
        Auth::logout();
        return response([
            'redirect' => route('login')
        ]);
    }
}
