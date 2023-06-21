<?php

namespace App\Http\Controllers;

use App\Models\Presenca;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class PresencaController extends Controller
{
    public function presenca(Request $request, $sessionId){
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        try{
            DB::beginTransaction();

            foreach($request->alunos as $key=>$value){
                Presenca::create([
                    'user_id' => $value,
                    'session_id' => $sessionId,
                    'present' => $request->presenca[$key]
                ]);
            }

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." registrou presença na sessão de ID ".$sessionId
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
}
