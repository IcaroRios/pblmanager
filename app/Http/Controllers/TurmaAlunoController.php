<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\TurmaAluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TurmaAlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $turmas = TurmaAluno::select("turmas.id as turma_id", "turmas.code as turma_code", "turmas.disciplina_ofertada_id as disciplina_id",
                "semestres.code as semestre_code", "disciplinas.code as disciplina_code", "disciplinas.name as disciplina_name",
                "class_days","class_time")
            ->join("turmas", "turma_alunos.turma_id", "turmas.id")
            ->join("disciplina_ofertadas", "turmas.disciplina_ofertada_id","disciplina_ofertadas.id")
            ->join("semestres", "disciplina_ofertadas.semestre_id","semestres.id")
            ->join("disciplinas","disciplina_ofertadas.disciplina_id","disciplinas.id")
            ->where("turma_alunos.user_id", Auth::user()->id)
            ->orderBy("semestre_code", "desc")
            ->get();

        if (!$turmas)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($turmas);
    }

    public function getStudentsByGrade($gradeId){
        $alunos = TurmaAluno::select("users.id as aluno_id", "users.first_name", "users.surname", "users.enrollment")
                        ->join('users', 'turma_alunos.user_id', 'users.id')
                        ->where("turma_alunos.turma_id", $gradeId)
                        ->orderBy("users.first_name", "ASC")
                        ->get();

        return response($alunos);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aluno = TurmaAluno::findOrFail($id);
        if (!$aluno)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($aluno);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $aluno = TurmaAluno::findOrFail($id);
        if (!$aluno)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $aluno->delete();

            DB::commit();
            return response(['message' => "O aluno foi removido da turma!"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
