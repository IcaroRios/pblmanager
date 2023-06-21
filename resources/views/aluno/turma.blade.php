@php
    
@endphp

@extends('layout.main')

@push("header")
<style type="text/css">
    .card-turma:hover {
      cursor: pointer;
    }
</style>
@endpush

@section("content")
@include('layout.navbar')
<v-app id="turma">
    <v-container>
      <v-row id="turma-title" class="mt-2">
        <v-col offset-md="12">
          <v-card >
            <v-card-title style="display: flex; justify-content:space-between;">
              <h4>@{{turma.disciplina_code}} - @{{turma.disciplina_name}} </h4>
            </v-card-title>
          </v-card>
        </v-col>
      </v-row>
        <v-row id="turma-title" class="mt-2">
            <v-col offset-md="12">
                <v-card >
                    <v-card-subtitle >
                        <v-btn
                            color="info"
                            @click="seeNotes()"
                        >
                            Ver Notas
                        </v-btn>
                        <v-card outlined v-for="problema in problemas" :key="problema.problema_id" class="mt-4">
                            <v-card-title>
                                <p>@{{problema.title}}</p>
                            </v-card-title>
                            <v-card-subtitle>
                                <p>@{{problema.created_at}}</p>
                                <v-divider class="mt-1"/>
                                <v-chip class="mt-3" outlined color="red" label :href="problema.anexo" target="_blank">
                                    <v-icon left>
                                        mdi-pdf-box
                                    </v-icon>
                                    Anexo do problema
                                </v-chip>
                            </v-card-subtitle>
                            <!-- <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    icon
                                    @click="problema.expand = !problema.expand"
                                >
                                    <v-icon>@{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                                </v-btn>
                            </v-card-actions> -->
                                <v-expand-transition>
                                <!-- <div v-show="problema.expand"> -->
                                <div>
                                    <v-card-text class="d-flex flex-row justify-content-start">
                                        <v-btn
                                        small
                                        class="mr-2"
                                        color="primary"
                                        style="background-color: var(--primary-dark-color) !important"
                                        @click="seeProblem(problema.problema_id)"
                                    >
                                        Visualizar Problema
                                    </v-btn>
                                    <v-btn small v-if="problema.anexo" class="btn btn-warning btn-sm" @click="downloadFile(problema)">
                                        Baixar Arquivo
                                    </v-btn>
                                </div>
                                </v-expand-transition>
                        </v-card>
                    </v-card-subtitle>
                </v-card>
            </v-col>
        </v-row>
      </v-tabs-items>
    <v-row>
        <v-col class="d-flex justify-end" cols="12" sm="12">
            @component('layout.components.notas') @endcomponent
        </v-col>
    </v-row>
    </v-container>
</v-app>
@endsection

@push("scripts")
<script>
    var turma = new Vue({
        el: '#turma',
        vuetify: new Vuetify(),
        data: {
            turma: {},
            problemas: [],
            waiting: false,
            mediaGeral: 0,

            //Notas
            problemaNotas: [],
            notasDialog: false,
        },

        created(){
            this.getTurma();
        },

        methods: {
            getTurma(){
                let splittedUrl = location.href.split('/');
                axios.get("{{route('turma-tutor.problemas-unidade', 'turmaId')}}".replace('turmaId', splittedUrl[splittedUrl.length - 1]))
                    .then(response => {
                        this.turma = response.data.turma;
                        this.problemas = response.data.problemas;
                    })
                    .catch(error => console.log(error.response.data))
            },
            seeNotes(){
                let splittedUrl = location.href.split('/');
                let route = "{{route('problema-unidade.ver-nota', ['alunoId', 'disciplinaOfertadaId'])}}";
                route = route.replace('alunoId', "{{Auth::user()->id}}");
                route = route.replace('disciplinaOfertadaId', splittedUrl[splittedUrl.length - 1]);
                axios.get(route)
                    .then(response => {
                        this.problemaNotas = [];
                        response.data.problemas.map(problemaNota => {
                            this.problemaNotas.push({
                                problema: problemaNota.title,
                                notas: problemaNota.notas,
                                media: problemaNota.media
                            });
                        });
                        this.mediaGeral = response.data.mediaGeral;
                        this.notasDialog = true;
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            seeProblem(problemaId){
                let route = "{{route('open-document', 'id')}}".replace('id', problemaId);
                window.open(route, "_blank");
            },
            downloadFile(problema){
                axios.post("{{route('file.download')}}", {path : problema.anexo})
                    .then(response => {
                        window.open(response.data, '_blank');
                    })
                    .catch(error => {
                        console.log(error);
                        error.response.data.message.forEach((item) => {
                            this.handleError(item);
                        });
                    })
            },
        }
    });
</script>
@endpush