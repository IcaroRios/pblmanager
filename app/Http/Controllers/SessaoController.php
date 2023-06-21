<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class SessaoController extends Controller
{
    public function index(){

    }

    public function store(Request $request){
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        try{
            DB::beginTransaction();

            $session = Sessao::create([
                'title'                => $request->title,
                'session_date'         => $request->session_date,
                'turma_id'             => $request->turma_id,
                'problema_unidades_id' => $request->problema_unidade_id,
            ]);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." criou a sessão ".$session->title." de ID ".$session->id
            ]);

            DB::commit();
            return response(['message' => 'Sessão criada com sucesso']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function update(Request $request, $id){

    }

    public function destroy($id){

    }
}
