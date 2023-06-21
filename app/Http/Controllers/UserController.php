<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\TutorRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        if (sizeof($users) == 0)
            return response([
                'message' => 'Nenhum registro localizado'
            ], 404);

        return response($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {

        $parts = explode(" ", $request->fullName);
        $surname = array_pop($parts);
        $firstname = implode(" ", $parts);

        try {
            DB::beginTransaction();
            $user = User::create([
                'first_name' => $firstname,
                'username' => $request->username,
                'email' => $request->email,
                'password'=> $request->password,
                'user_type' => 4,
                'enrollment' => $request->enrollment,
                'surname' => $surname,
            ]);

            if ($user->user_type == 1) $user->redirect = route('adm.menu');
            else if ($user->user_type == 2) $user->redirect = route('tutor.turmas');
            else if ($user->user_type == 4) $user->redirect = route('aluno.inicio');

            DB::commit();
            return response($user);
        } catch (Throwable $error) {
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tutorStore(TutorRequest $request)
    {
        if (Auth::user()->user_type != 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $parts = explode(" ", $request->fullName);
        $surname = array_pop($parts);
        $firstname = implode(" ", $parts);

        try {
            DB::beginTransaction();
            $user = User::create([
                'first_name' => $firstname,
                'username' => $request->username,
                'email' => $request->email,
                'password'=> $request->password,
                'user_type' => 2,
                'enrollment' => 00000000,
                'surname' => $surname,
            ]);

            DB::commit();
            return response($user);
        } catch (Throwable $error) {
            DB::rollBack();
            return response([
                'message' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user)
            return response([
                'message' => 'Nenhum registro localizado'
            ], 404);

        return response($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        if(!$request->username || !$request->password || !$request->email || !$request->enrollment ||
            !$request->user_types || !$request->first_name || !$request->surname
        )
            return response([
                'message' => "Campos inválidos"
            ], 400);

        if ($request->password < 6 || $request->password > 64)
            return response([
                'message' => "Senha inválida"
            ], 400);

        $user = User::where('id', $id)->first();
        if (!$user)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try {
            DB::beginTransaction();
            $user->update([
                'username' => $request->username,
                'email' => $request->email,
                'password'=> $request->password,
                'user_type' => $request->user_type,
                'enrollment' => $request->enrollment,
                'surname' => $request->surname,
            ]);
            DB::commit();
            return response(['message' => "Usuário alterado com sucesso"]);
        } catch (Throwable $error) {
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        if (!$user)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $user->delete();

            DB::commit();
            return response(['message' => "Usuário desativado"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function getByType($type){
        $users = User::select("id", "first_name", "surname",'email','username')->where('user_type', $type)->get();
        return response($users);
    }
}
