<?php

namespace App\Http\Controllers;

use App\Models\ProblemaAvaliacao;
use App\Models\ProblemaUnidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use Throwable;

class ProblemaUnidadeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $problemas = ProblemaUnidade::all();
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
        if (!$request->disciplina_ofertada_id || !$request->problema_id)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        try{
            DB::beginTransaction();

            $unidade = ProblemaUnidade::create([
                'disciplina_ofertada_id' => $request->disciplina_ofertada_id,
                'problema_id' => $request->problema_id,
            ]);

            DB::commit();
            return response($unidade);
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
        $unidade = ProblemaUnidade::findOrFail($id);
        if (!$unidade)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        return response($unidade);
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
        if (!$request->disciplina_ofertada_id || !$request->problema_id)
            return response([
                'message' => "Campos inválidos"
            ], 400);

        $unidade = ProblemaUnidade::findOrFail($id);
        if (!$unidade)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $unidade->update([
                'disciplina_ofertada_id' => $request->disciplina_ofertada_id,
                'problema_id' => $request->problema_id,
            ]);

            DB::commit();
            return response($unidade);
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
        $unidade = ProblemaUnidade::findOrFail($id);
        if (!$unidade)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        try{
            DB::beginTransaction();

            $unidade->delete();

            DB::commit();
            return response(['message' => "Problema da unidade excluído"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function applyNote(Request $request, $problemaId, $disciplinaOfertadaId){
        $problemaUnidade = ProblemaUnidade::where('problema_id', $problemaId)
                                        ->where('disciplina_ofertada_id', $disciplinaOfertadaId)
                                        ->first();
        if (!$problemaUnidade)
            return response([
                'message' => "Nenhum registro localizado"
            ], 404);

        $alunoJaTemNota = ProblemaAvaliacao::where('aluno_id', $request->aluno_id)
                                        ->where('problema_unidade_id', $problemaUnidade->id)
                                        ->first();
        if ($alunoJaTemNota)
            return response([
                'message' => "Aluno já possui nota para o problema"
            ], 400);

        try{
            DB::beginTransaction();

            ProblemaAvaliacao::create([
                'problema_unidade_id' => $problemaUnidade->id,
                'barema_id' => $request->barema_id,
                'feedback' => $request->feedback,
                'aluno_id' => $request->aluno_id
            ]);

            DB::commit();
            return response(['message' => "Nota aplicada"]);
        }catch(Throwable $error){
            DB::rollBack();
            return response([
                'error' => "Erro: ".$error->getMessage()."({$error->getLine()})"
            ], 500);
        }
    }

    public function seeNote($alunoId, $disciplinaOfertadaId){
        $problemaNota = ProblemaAvaliacao::select('problemas.title', 'avaliacao_problemas.feedback','avaliacao_problemas.barema_id')
                                        ->join('problema_unidades', 'problema_unidades.id', 'avaliacao_problemas.problema_unidade_id')
                                        ->join('disciplina_ofertadas', 'disciplina_ofertadas.id', 'problema_unidades.disciplina_ofertada_id')
                                        ->join('problemas', 'problemas.id', 'problema_unidades.problema_id')
                                        ->with("barema.item_baremas")
                                        ->where('aluno_id', $alunoId)
                                        ->where('problema_unidades.disciplina_ofertada_id', $disciplinaOfertadaId)
                                        ->get();

        $mediaGeral = 0;
        foreach ($problemaNota as $problema){
            $media = 0;
            $notaComPeso = [];

            foreach(json_decode($problema->feedback, true) as $avaliacao => $nota){
                $peso = $problema->barema->item_baremas->where('name',$avaliacao)->first()->amount;
                $media += $nota * $peso;
                $notaComPeso[] = ['avaliacao' => $avaliacao ,
                                  'peso'      => $peso,
                                  'nota'      => $nota];
            }
            $problema->notaComPeso = $notaComPeso;
            $problema->media = $media/100;
            $mediaGeral += $problema->media;
        }
        $mediaGeral = count($problemaNota) > 0 ? $mediaGeral / count($problemaNota) : 0;

        return response([
            'problemas' => $problemaNota,
            'mediaGeral' => $mediaGeral
        ]);
    }
}
