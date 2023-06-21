<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Request;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if($this->method() == 'PUT'){
            return [
                'email' => 'required|max:255',
            ];
        }else
            return [
                'username'   => 'required|unique:users|max:255',
                'fullName'   => 'required|max:255',
                'email'      => 'required|unique:users|max:255',
                'password'   => 'required|confirmed|min:6|max:255',
                'enrollment' => 'required|integer|unique:users',
            ];
    }

    public function attributes()
    {
        return [
            'username' => 'username',
            'fullName' => 'Nome',
            'email'    => 'Email',
            'password' => 'Senha',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'O email deve ser único',
            'username.unique' => 'O nome de usuário deve ser único',
            'enrollment.unique' => 'A matrícula já está cadastrada',
        ];
    }
}
