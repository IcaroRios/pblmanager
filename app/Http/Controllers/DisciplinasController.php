<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\disciplinas;
use App\Models\ProblemaUnidade;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DisciplinasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $disciplinas = Disciplina::get();

        return response($disciplinas);
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

        if (!$request->code || !$request->name || !$request->workload || !$request->departamento_id)
            return response([
                'message' => "Campos inválidos."
            ], 400);

        try{
            DB::beginTransaction();

            $path = "/disciplinas/{$request->code} - {$request->name}";

            $disciplina = Disciplina::create([
                'code' => $request->code,
                'name' => $request->name,
                'workload' => $request->workload,
                'departamento_id' => $request->departamento_id,
                'folder_id' => $path,
            ]);

            Storage::makeDirectory($path);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." adicionou a Disciplina ".$disciplina->code." de ID ".$disciplina->id
            ]);

            DB::commit();
            return response(['message' => 'Disciplina criada']);
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
        $disciplina = Disciplina::where('id', $id)->first();

        if (!$disciplina)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response ($disciplina);
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

        if (!$request->code || !$request->name || !$request->workload || !$request->departamento_id)
            return response([
                'message' => "Campos inválidos."
            ], 400);

        $disciplina = Disciplina::where('id', $id)->first();
        if (!$disciplina)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();


            $path = "/disciplinas/{$request->code} - {$request->name}";
            if (!Storage::exists($path))
                Storage::move($disciplina->folder_id, $path);

            $disciplina->update([
                'code' => $request->code,
                'name' => $request->name,
                'workload' => $request->workload,
                'departamento_id' => $request->departamento_id,
                'folder_id' => $path,
            ]);

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." editou a Disciplina ".$disciplina->code." de ID ".$disciplina->id
            ]);

            DB::commit();
            return response(['message' => 'Disciplina editada']);
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

        $disciplina = Disciplina::where('id', $id)
                ->first();
        if (!$disciplina)
            return response([
                    'message' => "Nenhum registro localizado"
                ], 404);

        try{
            DB::beginTransaction();

            $disciplina->delete();

            SystemLog::create([
                'log' => "Usuário ".Auth::user()->username." de ID ".Auth::user()->id." desativou a Disciplina ".$disciplina->code." de ID ".$disciplina->id
            ]);

            DB::commit();
            return response(['message' => 'Disciplina desativada']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function problemas($id){
        $problemas = ProblemaUnidade::select("problemas.id as problema_id", "problemas.title as problema_title", 
                                "problemas.description as problema_description", "semestres.code as semestre")
                            ->join("problemas", "problemas.id", "problema_unidades.problema_id")
                            ->join("disciplina_ofertadas", "disciplina_ofertadas.id", "problema_unidades.disciplina_ofertada_id")
                            ->join("disciplinas", "disciplinas.id", "disciplina_ofertadas.disciplina_id")
                            ->join("semestres", "semestres.id", "disciplina_ofertadas.semestre_id")
                            ->where("disciplinas.id", $id)
                            ->get();

        if (sizeof($problemas))
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($problemas);
    }

    //TODO getDisciplinasOfertadas retorna todas as vezes que a disciplina foi ofertada
}
