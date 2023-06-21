<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\DisciplinaOfertada;
use App\Models\ProblemaUnidade;
use App\Models\Semestre;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DisciplinaOfertadasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $disciplinaOfertadas = DisciplinaOfertada::select("disciplina_ofertadas.id as id", "disciplinas.code as disciplina_code",
                    "disciplinas.name as disciplina_name", "semestres.code as semestre", "disciplina_ofertadas.number_of_classes")
                ->join("disciplinas", "disciplina_ofertadas.disciplina_id", "disciplinas.id")
                ->join("semestres", "disciplina_ofertadas.semestre_id", "semestres.id")
                ->get();

        if (sizeof($disciplinaOfertadas) == 0)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($disciplinaOfertadas);
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

        if (!$request->semestre_id || !$request->disciplina_id || !$request->number_of_classes)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        try{
            DB::beginTransaction();

            $semestre = Semestre::where('id', $request->semestre_id)->first();
            $disciplina = Disciplina::where('id', $request->disciplina_id)->first();

            $path = "{$disciplina->folder_id}/{$semestre->code}";

            $disciplinaOfertada = DisciplinaOfertada::create([
                'disciplina_id' => $request->disciplina_id,
                'semestre_id' => $request->semestre_id,
                'number_of_classes' => $request->number_of_classes,
                'folder_id' => $path,
            ]);

            Storage::makeDirectory($path);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." ofertou a Disciplina ".$disciplinaOfertada->code." no Semestre ".$semestre->code
            ]);

            DB::commit();
            return response($disciplinaOfertada);
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
        $disciplinaOfertada = DisciplinaOfertada::select("disciplina_ofertadas.id as id", "disciplinas.code as disciplina_code",
                                    "disciplinas.name as disciplina_name", "semestres.code as semestre", "disciplina_ofertadas.number_of_classes")
                                ->join("disciplinas", "disciplina_ofertadas.disciplina_id", "disciplinas.id")
                                ->join("semestres", "disciplina_ofertadas.semestre_id", "semestres.id")
                                ->where('disciplina_ofertadas.id', $id)
                                ->first();

        if (!$disciplinaOfertada)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        return response($disciplinaOfertada);
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

        if (!$request->semestre_id || !$request->disciplina_id || !$request->number_of_classes)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        $disciplinaOfertada = DisciplinaOfertada::where('id', $id)->first();
        if (!$disciplinaOfertada)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $disciplinaOfertada->update([
                'disciplina_id' => $request->disciplina_id,
                'semestre_id' => $request->semestre_id,
                'number_of_classes' => $request->number_of_classes,
            ]);

            DB::commit();
            return response(['message' => "Disciplina ofertada editada"]);
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

        $disciplinaOfertada = DisciplinaOfertada::where('id', $id)->first();
        if (!$disciplinaOfertada)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $disciplinaOfertada->delete();

            DB::commit();
            return response(['message' => "Disciplina ofertada desativada"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function problemas($id){
        $problemas = ProblemaUnidade::select("problemas.id as problema_id", "problemas.title as problema_title", "problemas.description as problema_description")
                            ->join("problemas", "problemas.id", "problema_unidades.problema_id")
                            ->join("disciplina_ofertadas", "disciplina_ofertadas.id", "problema_unidades.disciplina_ofertada_id")
                            ->where("disciplina_ofertadas.id", $id)
                            ->get();

        if (sizeof($problemas))
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($problemas);
    }
}
