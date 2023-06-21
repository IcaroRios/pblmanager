<?php

namespace App\Http\Controllers;

use App\Models\ProblemaProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProblemaProdutoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->problema_id || !$request->item_name || !$request->amount)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        try{
            DB::beginTransaction();

            $produto = ProblemaProduto::create([
                'item_name' => $request->item_name,
                'problema_id' => $request->problema_id,
                'amount' => $request->amount,
            ]);

            DB::commit();
            return response($produto);
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
        $produto = ProblemaProduto::findOrFail($id);
        if (!$produto)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($produto);
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
        if (!$request->problema_id || !$request->item_name || !$request->amount)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        $produto = ProblemaProduto::findOrFail($id);
        if (!$produto)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $produto->update([
                'item_name' => $request->item_name,
                'problema_id' => $request->problema_id,
                'amount' => $request->amount,
            ]);

            DB::commit();
            return response($produto);
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
        $produto = ProblemaProduto::findOrFail($id);
        if (!$produto)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $produto->delete();

            DB::commit();
            return response(['message' => "O produto do problema foi removido do sistema!"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
