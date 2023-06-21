<?php

namespace App\Http\Controllers;

use App\Models\Barema;
use App\Models\baremas;
use App\Models\ItemBarema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class BaremasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $baremas = Barema::with(['item_baremas', 'problema'])
                        ->where('problema_id', $request->problema)
                        ->get();

        return response($baremas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        if (!$request->name)
            return response([
                'error' => 'Campos inválidos'
            ], 400);

        try{
            DB::beginTransaction();

            $novoBarema = Barema::create([
                'name' => $request->name,
                'problema_id' => $request->problema_id
            ]);

            foreach($request->itens as $item){
                if (!$item['name'] && !$item['amount']){
                    DB::rollBack();
                    return response([
                        'error' => 'Campos inválidos'
                    ], 400);
                }

                ItemBarema::create([
                    'barema_id' => $novoBarema->id,
                    'name' => $item['name'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();
            return response(['message' => "Barema criado"]);
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
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $barema = Barema::with(['item_baremas'])
                ->where('id', $id)
                ->first();
        if (!$barema)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($barema);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        if (!$request->name)
            return response([
                'error' => 'Campos inválidos'
            ], 400);

        try{
            DB::beginTransaction();

            $barema = Barema::where('id', $id)->first();
            if (!$barema)
                return response([
                    'message' => "Nenhum registro localizado"
                ], 404);

            $barema->update([
                'name' => $request->name
            ]);

            $itens = ItemBarema::where('barema_id', $barema->id)->get();
            foreach($itens as $item){
                if (!$item->deleted_at){
                    $item->delete();
                }
            }

            foreach($request->itens as $item){
                if (!$item['name'] && !$item['amount']){
                    DB::rollBack();
                    return response([
                        'error' => 'Campos inválidos'
                    ], 400);
                }

                ItemBarema::create([
                    'barema_id' => $barema->id,
                    'name' => $item['name'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();
            return response(['message' => "Barema editado"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()
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
        if (Auth::user()->user_type == 1)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $barema = Barema::where('id', $id)->first();
        if (!$barema)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();
            $barema->delete();
            ItemBarema::where('barema_id', $barema->id)->delete();

            DB::commit();
            return response(['message' => "Barema desativado"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()
            ], 500);
        }
    }
}
