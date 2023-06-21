<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\ArquivoCliente;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Cliente $client)
    {
        return view('dashboard.arquivo.create',compact('client'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->arquivo){
            $arquivo = $request->arquivo;
            $allowedfileExtension=['pdf','aspx'];
            $extension = $arquivo->getClientOriginalExtension();
            $check=in_array($extension,$allowedfileExtension);
            if ($check){
                $filepath = $arquivo->store('client/arquivos/'.$request->cliente_id);
                ArquivoCliente::create([
                    'descricao'       => $request->descricao,
                    'caminho_arquivo' => $filepath,
                    'cliente_id'      => $request->cliente_id
                ]);
                return redirect()->back()->with(['success' => 'Arquivo adicionado']);
            }
            else
                return redirect()->back()>with(['warning' => 'Formato de arquivo incorreto!']);
        }
        return redirect()->back()>with(['warning' => 'Arquivo não enviado}!']);
    }

    public function download(ArquivoCliente $arquivo){
        try{
            return response()->download(Storage_path('app/'.$arquivo->caminho_arquivo),null,[],null);
        }
        catch(\Exception $e){
            return 'Arquivo não encontrado';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArquivoCliente $arquivo)
    {
        Storage::delete($arquivo->caminho_arquivo);
        $arquivo->delete();
        return redirect()->back()->with(['success' => 'Arquivo Deletado!']);
    }
}
