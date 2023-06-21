<?php

namespace App\Http\Controllers;

use App\Models\ProblemaUnidade;
use App\Models\Sessao;
use App\Models\SystemLog;
use App\Models\Turma;
use App\Models\turma_tutors;
use App\Models\TurmaAluno;
use App\Models\TurmaTutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TurmaTutorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $turmaTutor = TurmaTutor::all();

        return response($turmaTutor);
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

        if (!$request->user_id || !$request->turma_id)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        try{
            DB::beginTransaction();

            $turmaTutor = TurmaTutor::create([
                'turma_id' => $request->turma_id,
                'user_id' => $request->user_id
            ]);

            $tutor = User::where('id', $turmaTutor->user_id)->first();
            $turma = Turma::where('id', $turmaTutor->turma_id)->first();

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." designou o Tutor ".$tutor->first_name." ". $tutor->surname.
                        " de ID ".$tutor->id." à Turma ".$turma->code." de ID ".$turma->id." integrante da Disciplina Ofertada de ID ".$turma->disciplina_id.
                        "Data de Designação: ".$turmaTutor->created_at,
            ]);

            DB::commit();
            return response($turmaTutor);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function problemas(){
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $problemas = TurmaTutor::select("problemas.title", "problema_unidades.created_at", "disciplinas.code as disciplina_code",
                    "disciplinas.name as disciplina_name", "problema_unidades.problema_id as problema_id", "semestres.code as semestre_code",
                    "disciplina_ofertadas.id as disciplina_id")
                ->join("turmas", "turma_tutors.turma_id", "turmas.id")
                ->join('users', 'users.id', 'turma_tutors.user_id')
                ->join("disciplina_ofertadas", 'turmas.disciplina_ofertada_id',"disciplina_ofertadas.id")
                ->join("semestres", "disciplina_ofertadas.semestre_id","semestres.id")
                ->join("disciplinas","disciplina_ofertadas.disciplina_id","disciplinas.id")
                ->join("problema_unidades", "disciplina_ofertadas.id", "problema_unidades.disciplina_ofertada_id")
                ->join("problemas", "problema_unidades.problema_id", "problemas.id")
                ->where("users.id", Auth::user()->id)
                ->get();

        return response($problemas);
    }

    public function problemaUnidade($turmaId){
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $problemas = TurmaTutor::select("problemas.title", "problema_unidades.created_at", "disciplinas.code as disciplina_code",
                    "disciplinas.name as disciplina_name", "problema_unidades.problema_id as problema_id","problema_unidades.id", "problemas.file_path as anexo")
                ->join("turmas", "turma_tutors.turma_id", "turmas.id")
                ->join("disciplina_ofertadas", 'turmas.disciplina_ofertada_id',"disciplina_ofertadas.id")
                ->join("semestres", "disciplina_ofertadas.semestre_id","semestres.id")
                ->join("disciplinas","disciplina_ofertadas.disciplina_id","disciplinas.id")
                ->join("problema_unidades", "disciplina_ofertadas.id", "problema_unidades.disciplina_ofertada_id")
                ->join("problemas", "problema_unidades.problema_id", "problemas.id")
                ->where("turmas.id", $turmaId)
                ->get();

        $turma = TurmaTutor::select("disciplinas.code as disciplina_code", "disciplinas.name as disciplina_name",
                    "disciplina_ofertadas.id as disciplina_id", "turmas.id as turma_id")
                ->join("turmas", "turma_tutors.turma_id", "turmas.id")
                ->join("disciplina_ofertadas", 'turmas.disciplina_ofertada_id',"disciplina_ofertadas.id")
                ->join("semestres", "disciplina_ofertadas.semestre_id","semestres.id")
                ->join("disciplinas","disciplina_ofertadas.disciplina_id","disciplinas.id")
                ->where("turmas.id", $turmaId)
                ->first();

        $alunosMatriculados = TurmaAluno::select("users.id as aluno_id", "users.first_name", "users.surname", "users.enrollment")
            ->join('users', 'turma_alunos.user_id', 'users.id')
            ->where("turma_alunos.turma_id", $turma->turma_id)
            ->orderBy("users.first_name", "ASC")
            ->get();

        $todosAlunos = User::select('id','first_name','surname','enrollment')
            ->where('user_type',4)
            ->orderBy("first_name", "ASC")
            ->get();

        $sessoes = Sessao::with('presencas')
            ->where('turma_id', $turma->turma_id)
            ->select("sessoes.id","sessoes.title","sessoes.session_date","sessoes.turma_id","sessoes.problema_unidades_id","sessoes.created_at","sessoes.updated_at","problemas.title as probema_title")
            ->join("problema_unidades", "sessoes.problema_unidades_id", "problema_unidades.id")
            ->join("problemas", "problema_unidades.problema_id", "problemas.id")
            ->orderBy('session_date', 'ASC')
            ->get();

        if (!$turma)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response([
            'turma' => $turma,
            'problemas' => $problemas,
            'alunos' => $alunosMatriculados,
            'sessoes' => $sessoes,
            'todosAlunos' => $todosAlunos
        ]);
    }

    public function turmas()
    {
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $turmas = TurmaTutor::select("turmas.id as turma_id", "turmas.code as turma_code", "turmas.disciplina_ofertada_id as disciplina_id",
                "semestres.code as semestre_code", "disciplinas.code as disciplina_code", "disciplinas.name as disciplina_name",
                "class_days","class_time")
            ->join("turmas", "turma_tutors.turma_id", "turmas.id")
            ->join("disciplina_ofertadas", 'turmas.disciplina_ofertada_id',"disciplina_ofertadas.id")
            ->join("semestres", "disciplina_ofertadas.semestre_id","semestres.id")
            ->join("disciplinas","disciplina_ofertadas.disciplina_id","disciplinas.id")
            ->where("turma_tutors.user_id", Auth::user()->id)
            ->orderBy("semestre_code", "desc")
            ->get();

        if (!$turmas)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($turmas);
    }

    public function matricular(Request $request, $id){
        if (Auth::user()->user_type != 2)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);
        try{
            DB::beginTransaction();

            foreach($request->alunos as $key=>$value){
                if($value){
                    $alunoMatriculado = TurmaAluno::where('turma_id', $id)->where('user_id', $key)->first();
                    if(!$alunoMatriculado)
                        TurmaAluno::create([
                            'turma_id' => $id,
                            'user_id' => $key
                        ]);
                }
            }

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." matriculou aluno na turma: ".$id
            ]);

            DB::commit();
            return response(['message' => 'Matriculado com sucesso']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
