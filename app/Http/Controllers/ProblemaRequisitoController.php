<?php

namespace App\Http\Controllers;

use App\Models\ProblemaRequisito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProblemaRequisitoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->problema_id || !$request->title || !$request->description)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        try{
            DB::beginTransaction();

            $requisito = ProblemaRequisito::create([
                'title' => $request->title,
                'problema_id' => $request->problema_id,
                'description' => $request->description,
            ]);

            DB::commit();
            return response($requisito);
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
        $requisito = ProblemaRequisito::findOrFail($id);
        if (!$requisito)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($requisito);
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
        if (!$request->problema_id || !$request->title || !$request->description)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        $requisito = ProblemaRequisito::findOrFail($id);
        if (!$requisito)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $requisito->update([
                'title' => $request->title,
                'problema_id' => $request->problema_id,
                'description' => $request->description,
            ]);

            DB::commit();
            return response($requisito);
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
        $requisito = ProblemaRequisito::findOrFail($id);
        if (!$requisito)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $requisito->delete();

            DB::commit();
            return response(['message' => "O requisito do problema foi removido do sistema!"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
