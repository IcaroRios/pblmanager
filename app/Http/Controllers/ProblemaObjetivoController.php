<?php

namespace App\Http\Controllers;

use App\Models\ProblemaObjetivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProblemaObjetivoController extends Controller
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

            $objetivo = ProblemaObjetivo::create([
                'problema_id' => $request->problema_id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

            DB::commit();
            return response($objetivo);
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
        $objetivo = ProblemaObjetivo::findOrFail($id);
        if (!$objetivo)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($objetivo);
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

        $objetivo = ProblemaObjetivo::findOrFail($id);
        if (!$objetivo)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $objetivo->update([
                'problema_id' => $request->problema_id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

            DB::commit();
            return response($objetivo);
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
        $objetivo = ProblemaObjetivo::findOrFail($id);
        if (!$objetivo)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $objetivo->delete();

            DB::commit();
            return response(['message' => "O objetivo do problema foi removida do sistema!"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
