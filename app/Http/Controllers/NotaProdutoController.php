<?php

namespace App\Http\Controllers;

use App\Models\NotaProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotaProdutoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->produto_id || !$request->grade)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        try{
            DB::beginTransaction();

            $nota = NotaProduto::create([
                'produto_id' => $request->produto_id,
                'grade' => $request->grade
            ]);

            DB::commit();
            return response($nota);
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
        $nota = NotaProduto::findOrFail($id);
        if (!$nota)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($nota);
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
        if (!$request->produto_id || !$request->grade)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        $nota = NotaProduto::findOrFail($id);
        if (!$nota)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $nota->update([
                'produto_id' => $request->produto_id,
                'grade' => $request->grade
            ]);

            DB::commit();
            return response($nota);
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
        $nota = NotaProduto::findOrFail($id);
        if (!$nota)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $nota->delete();

            DB::commit();
            return response(['message' => "A nota do problema foi removida do sistema!"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
