<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\departamentos;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class DepartamentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departamentos = Departamento::get();
        return response($departamentos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->user_type == 2)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        if (!$request->name || !$request->abbreviation)
            return response([
                'message' => "Campos inválidos."
            ], 400);

        $abreviacaoJaExiste = Departamento::where('abbreviation', $request->abbreviation)->first();
        if ($abreviacaoJaExiste)
            return response([
                'message' => "Já existe um departamento cadastrado com esta sigla."
            ], 400);

        try{
            DB::beginTransaction();
            $departamento = Departamento::create([
                'name' => $request->name,
                'abbreviation' => $request->abbreviation,
            ]);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." adicionou o Departamento ".$departamento->abbreviation." de ID ".$departamento->id
            ]);

            DB::commit();
            return response(['message' => 'Departamento cadastrado']);
        }catch(Throwable $error){
            DB::rollBack();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $departamento = Departamento::where('id', $id)->first();

        if (!$departamento)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response ($departamento);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->user_type == 2)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $departamento = Departamento::where('id', $id)->first();
        if (!$departamento)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        if (!$request->name || !$request->abbreviation)
            return response([
                'message' => "Campos inválidos."
            ], 400);

        try{
            DB::beginTransaction();
            $departamento->update([
                'name' => $request->name,
                'abbreviation' => $request->abbreviation,
            ]);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." editou o Departamento ".$departamento->abbreviation." de ID ".$departamento->id
            ]);

            DB::commit();
            return response(['message' => 'Departamento editado']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->user_type == 2)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $departamento = Departamento::where('id', $id)->first();
        if (!$departamento)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $departamento->delete();

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." desativou o Departamento ".$departamento->abbreviation." de ID ".$departamento->id
            ]);

            DB::commit();
            return response(['message' => 'Departamento desativado']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
