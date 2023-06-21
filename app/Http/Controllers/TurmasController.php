<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\DisciplinaOfertada;
use App\Models\SystemLog;
use App\Models\Turma;
use App\Models\TurmaTutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TurmasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $turmas = Turma::select("turmas.id as id", "turmas.code as code", "disciplina_ofertadas.id as disciplina_ofertada_id",
                "disciplinas.code as disciplina_code", "disciplinas.name as disciplina_name", "semestres.code as semestre_code",
                "class_days", "class_time", "semestres.id as semestre_id")
                ->join("disciplina_ofertadas", 'turmas.disciplina_ofertada_id', "disciplina_ofertadas.id")
                ->join("disciplinas", "disciplina_ofertadas.disciplina_id","disciplinas.id")
                ->join("semestres","disciplina_ofertadas.semestre_id","semestres.id")
                ->get();

        return response($turmas);
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

        if (!$request->disciplina_id || !$request->code || !$request->class_days || !$request->class_time)
            return response([
                "message" => "Campos inválidos"
            ], 400);

        try{
            DB::beginTransaction();

            $disciplinaOfertada = DisciplinaOfertada::where('id', $request->disciplina_id)->first();

            $path = "{$disciplinaOfertada->folder_id}/{$request->code}";

            $turma = Turma::create([
                'code' => $request->code,
                'disciplina_ofertada_id' => $request->disciplina_id,
                'class_days' => $request->class_days,
                'class_time' => $request->class_time,
                'folder_id' => $path
            ]);

            Storage::makeDirectory($path);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." criou a Turma ".$turma->code." de ID ".$turma->id
            ]);

            DB::commit();
            return response(['message' => 'Turma criada']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
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
        $turma = Turma::select("turmas.id as id", "turmas.code as code", "disciplina_ofertadas.id as disciplina_ofertada_id",
                        "disciplinas.code as disciplina_code", "disciplinas.name as disciplina_name", "semestres.code as semestre_code",
                        "class_days", "class_time")
                    ->join("disciplina_ofertadas", 'turmas.disciplina_ofertada_id', "disciplina_ofertadas.id")
                    ->join("disciplinas", "disciplina_ofertadas.disciplina_id","disciplinas.id")
                    ->join("semestres","disciplina_ofertadas.semestre_id","semestres.id")
                    ->where('turmas.id', $id)
                    ->first();

        if (!$turma)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        return response($turma);
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

        $turma = Turma::findOrFail($id);
        if (!$turma)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $disciplinasOfertadas = DisciplinaOfertada::where('id', $turma->id)->get();
            foreach($disciplinasOfertadas as $disciplina){
                SystemLog::create([
                    'log' => "Disciplina Ofertada de ID {$disciplina->id} removida por não possuir mais turmas.",
                ]);
                $disciplina->delete();
            }
            $turma->turma_tutors()->delete();
            $turma->delete();

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." excluiu a Turma ".$turma->code." de ID ".$turma->id
            ]);

            DB::commit();
            return response(['message' => 'Turma removida do sistema']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function tutores($turmaId){
        $turmaTutor = User::select("users.first_name", "users.surname", "users.id as user_id", "turmas.id as turma_id")
            ->join("turma_tutors", "turma_tutors.user_id", "users.id")
            ->join("turmas", "turmas.id", "turma_tutors.turma_id")
            ->where("turmas.id", $turmaId)
            ->get();

        if (!$turmaTutor)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        return response($turmaTutor);
    }
}
