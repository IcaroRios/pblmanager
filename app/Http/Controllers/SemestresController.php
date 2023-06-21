<?php

namespace App\Http\Controllers;

use App\Models\DisciplinaOfertada;
use App\Models\Semestre;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class SemestresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semestres = Semestre::get();
        return response ($semestres);
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

        if (!$request->code || !$request->start_date || !$request->end_date)
            return response([
                "message" => "Campos inválidos"
            ], 400);

        $codigoJaExiste = Semestre::where('code', $request->code)->first();
        if ($codigoJaExiste)
            return response([
                "message" => "Já existe um semestre cadastrado com este código."
            ], 400);

        if ($request->start_date == $request->end_date)
            return response([
                "message" => "A data de término do semestre deve ser diferente da data de início"
            ], 400);

        try{
            DB::beginTransaction();

            $semestre = Semestre::create([
                'code' => $request->code,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." adicionou o Semestre ".$semestre->code." de ID ".$semestre->id
            ]);

            DB::commit();
            return response(['message' => 'Semestre criado']);
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
        $semestre = Semestre::where('id', $id)->first();
        if (!$semestre)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response ($semestre);
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

        if (!$request->code || !$request->start_date || !$request->end_date)
            return response([
                "message" => "Campos inválidos"
            ], 400);

        $semestre = Semestre::where('id', $id)->first();
        if (!$semestre)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        $codigoJaExiste = Semestre::where('code', $request->code)->where('id', '<>', $id)->first();
        if ($codigoJaExiste)
            return response([
                "message" => "Já existe um semestre cadastrado com este código."
            ], 400);

        if ($request->start_date == $request->end_date)
            return response([
                "message" => "A data de término do semestre deve ser diferente da data de início"
            ], 400);

        try{
            DB::beginTransaction();

            $semestre->update([
                'code' => $request->code,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." editou o Semestre ".$semestre->code." de ID ".$semestre->id
            ]);

            DB::commit();
            return response(['message' => 'Semestre editado']);
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

        $semestre = Semestre::where('id', $id)->first();
        if (!$semestre)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $semestre->delete();

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." desativou o Semestre ".$semestre->code." de ID ".$semestre->id
            ]);

            DB::commit();
            return response(['message' => 'Semestre desativado']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function disciplinasOfertadas($semestreId){
        $disciplinasOfertadas = DisciplinaOfertada::select("disciplina_ofertadas.id", "disciplinas.code", "disciplinas.name")
                                            ->join("disciplinas", "disciplina_ofertadas.disciplina_id", "disciplinas.id")
                                            ->join("semestres", "disciplina_ofertadas.semestre_id", "semestres.id")
                                            ->where('disciplina_ofertadas.active', true)
                                            ->where('semestres.id', $semestreId)
                                            ->get();

        if (sizeof($disciplinasOfertadas) == 0)
            return response([
                'message' => "Nenhum registro de disciplina ofertada para este semestre foi encontrado!",
            ], 404);

        return response($disciplinasOfertadas);
    }

    //TODO getProblemasSemestre retorna todos problemas unidade do semestre
}
