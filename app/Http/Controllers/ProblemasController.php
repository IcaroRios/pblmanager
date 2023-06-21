<?php

namespace App\Http\Controllers;

use App\Models\Problema;
use App\Models\ProblemaUnidade;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProblemasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $problemas = Problema::get();
        return response ($problemas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->disciplina_ofertada_id || !$request->title || !$request->description)
            return response([
                "message" => "Campos inválidos"
            ], 400);

        $anexo = $request->file('anexo');
        if (!$request->body && !$anexo)
            return response([
                "message" => "O problema precisa ter pelo menos uma forma de conteúdo."
            ], 400);

        try{
            DB::beginTransaction();

            if ($anexo){
                $filePath = $anexo->store('public/uploads/');
            }

            $problema = Problema::create([
                'title' => $request->title,
                'description' => $request->description,
                'body' => $request->body ?? null,
                'file_path' => $filePath ?? null,
            ]);

            ProblemaUnidade::create([
                'disciplina_ofertada_id' => $request->disciplina_ofertada_id,
                'problema_id' => $problema->id,
                'data_entrega' => $request->data_entrega,
            ]);

            DB::commit();
            return response(['message' => 'Problema criado']);
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
        $problema = Problema::select('problemas.title', "problemas.description", "problemas.body", "problemas.file_path", "problema_unidades.data_entrega")
                        ->join("problema_unidades", "problema_unidades.problema_id", "problemas.id")
                        ->where('problemas.id', $id)->first();
        if (!$problema)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response ($problema);
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
        if (!$request->title || !$request->description)
            return response([
                "message" => "Campos inválidos"
            ], 400);

        $problema = Problema::where('id', $id)->first();
        if (!$problema)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        $anexo = $request->file('anexo');
        if (!$request->body && !$anexo)
            return response([
                "message" => "O problema precisa ter pelo menos uma forma de conteúdo."
            ], 400);

        if ($anexo){
            Storage::delete($problema->file_path);
            $filePath = $anexo->store('public/uploads');
        }

        try{
            DB::beginTransaction();

            $problema->update([
                'title' => $request->title ?? $problema->title,
                'description' => $request->description ?? $problema->description,
                'body' => $request->body ?? $problema->body,
                'file_path' => $filePath ?? $problema->file_path,
            ]);

            ProblemaUnidade::where('problema_id', $problema->id)->update([
                'data_entrega' => $request->data_entrega
            ]);

            DB::commit();
            return response(['message' => 'Problema editado']);
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
        $problema = Problema::where('id', $id)->first();
        if (!$problema)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $problema->delete();

            DB::commit();
            return response(['message' => 'Problema desativado']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function copy(Request $request){
        $problema = Problema::select('problemas.id', 'problemas.title', "problemas.description", "problemas.body", "problemas.file_path", "problema_unidades.data_entrega")
                            ->join("problema_unidades", "problema_unidades.problema_id", "problemas.id")
                            ->where('problemas.id', $request->problema_id)
                            ->first();
        if (!$problema)
            return response([
                "message" => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            if ($problema->file_path){
                $splittedFilePath = explode('.', $problema->file_path);
                $extension = $splittedFilePath[sizeof($splittedFilePath) - 1];
                $file = Storage::disk('local')->path($problema->file_path);
                $copyPath = Storage::putFileAs('public/uploads', $file, \Str::random(40).'.'.$extension);
            }

            $copyProblema = Problema::create([
                'title' => $problema->title,
                'description' => $problema->description,
                'body' => $problema->body ?? null,
                'file_path' => $copyPath ?? null,
            ]);

            ProblemaUnidade::create([
                'disciplina_ofertada_id' => $request->disciplina_id,
                'problema_id' => $copyProblema->id,
                'data_entrega' => $problema->data_entrega,
            ]);

            DB::commit();
            return response(['message' => 'Problema copiado']);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }
}
