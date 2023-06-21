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
              <v-spacer></v-spacer>
              <v-btn
                @click="matricularAlunos"
                color="primary"
                style="background-color: var(--primary-dark-color) !important"
               >
                Matricular aluno
              </v-btn>
              <v-spacer></v-spacer>
                <v-btn
                color="primary"
                style="background-color: var(--primary-dark-color)"
                href="{{route('tutor.turmas')}}"
                >
                    Voltar
                </v-btn>
            </v-card-title>
          </v-card>
        </v-col>
      </v-row>
      <v-tabs class="mt-2" v-model="tab" grow>
        <v-tab>Alunos</v-tab>
        <v-tab>Sessões</v-tab>
        <v-tab>Problemas</v-tab>
      </v-tabs>
      <v-tabs-items v-model="tab">
        <v-tab-item>
            <v-row class="mt-5">
                <v-col offset=12>

                </v-col>
                <v-col class="d-flex" cols="12" sm="12">
                    <v-text-field
                        align="center"
                        v-model="studentSearchQuery"
                        append-icon="mdi-magnify"
                        label="Pesquisar..."
                        hide-details
                        dense
                    ></v-text-field>
                </v-col>
                <v-col cols="12" sm="12">
                    <v-data-table
                    :loading="studentData == undefined"
                    :items="studentFilteredList"
                    :headers="studentHeaders"
                    loading-text="Carregando... Por favor, aguarde"
                    :items-per-page="10"
                    class="elevation-1 row-pointer table"
                    :no-data-text="studentNotFound"
                    >
                    <template v-slot:item.action="{ item }">
                        <v-btn
                            @click="seeNotes(item)"
                            elevation="2"
                            small
                            color="info"
                            tile
                            class="ml-2"
                        >
                            <v-icon left> mdi-file </v-icon> Notas
                        </v-btn>
                    </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-tab-item>
        <v-tab-item>
            <v-row class="mt-5">
                <v-col offset=12>
                    @component('tutor.modals.sessao')@endcomponent
                    @component('tutor.modals.presenca')@endcomponent
                    {{-- <v-btn
                        color="primary"
                        style="background-color: var(--primary-dark-color) !important"
                        href="{{route('tutor.adicionar-sessão',request()->route()->id)}}" >
                        Nova Sessão
                    </v-btn> --}}
                </v-col>
                <v-col class="d-flex mt-5" cols="12" sm="12">
                    <v-text-field
                    align="center"
                    v-model="sessionSearchQuery"
                    append-icon="mdi-magnify"
                    label="Pesquisar..."
                    hide-details
                    dense
                    ></v-text-field>
                </v-col>
                <v-col offset="12">
                    <v-data-table
                        :loading="sessionData == undefined"
                        :items="sessionFilteredList"
                        :headers="sessionHeaders"
                        loading-text="Carregando... Por favor, aguarde"
                        :items-per-page="10"
                        class="elevation-1 row-pointer table"
                        :no-data-text="sessionNotFound"
                    >
                        <template v-slot:item.action="{ item }">
                            <v-btn
                                @click="startPresenca(item)"
                                elevation="2"
                                small
                                color="warning"
                                tile
                                :disabled="item.presencas.length > 0"
                            >
                                <v-icon left> mdi-pencil </v-icon>Presença</v-btn
                            >
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-tab-item>
        <v-tab-item>
            <v-row id="turma-title" class="mt-2">
                <v-col offset-md="12">
                  <v-card >
                      <v-card-title>
                            <v-btn
                                color="primary"
                                style="background-color: var(--primary-dark-color) !important"
                                :href=novoProblemaLink >
                                Novo Problema
                            </v-btn>
                      </v-card-title>

                      <v-card-subtitle >
                          <v-card outlined v-for="problema in problemas" :key="problema.problema_id" class="mt-4">
                              <v-card-title>
                                  <p>@{{problema.title}}</p>
                              </v-card-title>
                              <v-card-subtitle>
                                  <p>@{{problema.created_at}}</p>
                                  <v-divider class="mt-1"/>
                              </v-card-subtitle>
                                  <v-expand-transition>
                                  <div>
                                      <v-card-text class="d-flex flex-row justify-content-start">
                                          <v-btn
                                            small
                                            class="mr-2"
                                            color="primary"
                                            style="background-color: var(--primary-dark-color) !important"
                                            @click="setDataToNote(problema.problema_id)"
                                        >
                                            Atribuir Nota
                                        </v-btn>
                                        <v-btn
                                            small
                                            color="primary"
                                            class="mr-2"
                                            style="background-color: var(--secondary-light-color) !important"
                                            @click="setDataToUpdate(problema)"
                                        >
                                            Editar Problema
                                        </v-btn>
                                        <v-btn
                                            small
                                            color="danger"
                                            class="mr-2"
                                            @click="setDataToCopy(problema)"
                                        >
                                            Copiar Problema
                                        </v-btn>
                                        <v-btn
                                            small
                                            color="danger"
                                            class="mr-2"
                                            @click="goToBarema(problema.problema_id)"
                                        >
                                            Barema
                                        </v-btn>
                                        <v-btn
                                            small
                                            color="danger"
                                            class="mr-2"
                                            @click="seeProblem(problema.problema_id)"
                                        >
                                            Visualizar Problema
                                        </v-btn>
                                        <v-btn small v-if="problema.anexo" class="btn btn-warning btn-sm" @click="downloadFile(problema)">
                                            Baixar Arquivo
                                        </v-btn>
                                      </v-card-text>
                                  </div>
                                  </v-expand-transition>
                          </v-card>
                      </v-card-subtitle>
                  </v-card>
                </v-col>
            </v-row>
        </v-tab-item>
      </v-tabs-items>
    <v-row>
        <v-col class="d-flex justify-end" cols="12" sm="12">
            @component('tutor.modals.copiar-problema') @endcomponent
        </v-col>
        <v-col class="d-flex justify-end" cols="12" sm="12">
            @component('layout.components.notas') @endcomponent
        </v-col>
        <v-col class="d-flex justify-end" cols="12" sm="12">
            @component('layout.components.matricular-aluno') @endcomponent
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
            tutorTurmas: [],
            problemas: [],
            waiting: false,
            dataToCopy: undefined,
            selectedTutorTurma: "",
            mediaGeral: 0,

            dialog: false,
            validForm: undefined,
            tab: "",

            studentSearchQuery: "",
            studentData: [],
            studentHeaders: {},

            sessionSearchQuery: "",
            sessionData: [],
            sessionHeaders: {},

            copyDialog: false,

            //Session Methods
            form: {
                title: "",
                problema_unidade_id: "",
                session_date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
            },
            dateFormatted: "",
            menu1: false,
            dialog: false,
            stored: false,
            update: false,
            updated: false,
            nameRules: [(v) => !!v || "Nome da sessão é um campo obrigatório"],
            problemRules: [(v) => !!v || "Selecionar um problema é um campo obrigatório"],
            errorMessages: { name: null },

            //Presenca
            presencaDialog: false,
            sessionId: null,
            presencaForm: {
                alunos:[],
                presenca:[]
            },

            //Notas
            problemaNotas: [],
            notasDialog: false,

            //Matricula
            todosAlunos: [],
            matriculaForm: {
                alunos:[],
            },
            matriculaDialog: false,
            novoProblemaLink: ""
        },

        computed:{
            studentFilteredList() {
              let search = this.removeSpecial(this.studentSearchQuery.toLowerCase().trim());
              var itens = this.studentData;
              if (search != "") {
                  return itens.filter((item) => {
                    return (
                        this.removeSpecial(item.name.toLowerCase()).includes(search) ||
                        this.removeSpecial(item.enrollment.toLowerCase()).includes(search)
                    );
                  });
              } else {
                return itens;
              }
          },

          sessionFilteredList() {
              let search = this.removeSpecial(this.sessionSearchQuery.toLowerCase().trim());
              var itens = this.sessionData;
              if (search != "") {
                  return itens.filter((item) => {
                    return (
                        this.removeSpecial(item.title.toLowerCase()).includes(search) ||
                        this.removeSpecial(item.session_date.toLowerCase()).includes(search)
                    );
                  });
              } else {
                return itens;
              }
          },

          studentNotFound() {
              if (this.studentData == undefined || this.studentData.length == 0) {
                  return "Não há alunos na turma";
              } else {
                  return "Nenhum aluno encontrado";
              }
          },

          sessionNotFound() {
              if (this.sessionData == undefined || this.sessionData.length == 0) {
                  return "Não há sessões na turma";
              } else {
                  return "Nenhum sessão encontrado";
              }
          },

          snackText() {
                return this.updated == false
                    ? "Sessão Adicionada com Sucesso!"
                    : "Sessão Atualizada com Sucesso!";
                },
            computedDateFormatted () {
                return this.formatDate(this.date)
            },
        },

        watch: {
            form:{
                handler(){
                    this.dateFormatted = this.formatDate(this.form.session_date)
                },
                deep: true
            },
        },

        created(){
            this.dateFormatted = this.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10));
            this.studentHeaders = [
                { text: "Nome", value: "name"},
                { text: "Matricula", value: "enrollment"},
                { text: "Actions", value: "action", sortable: false }
            ];

            this.sessionHeaders = [
                { text: "Problema", value: "probema_title"},
                { text: "Nome", value: "title"},
                { text: "Data", value: "session_date"},
                { text: "Actions", value: "action", sortable: false }
            ];
            this.getTurma();
            this.getTurmas();
        },

        methods: {
            removeSpecial(texto) {
                texto = texto.replace(/[ÀÁÂÃÄÅ]/, "A");
                texto = texto.replace(/[àáâãäå]/, "a");
                texto = texto.replace(/[ÈÉÊË]/, "E");
                texto = texto.replace(/[Ç]/, "C");
                texto = texto.replace(/[ç]/, "c");
                return texto;
            },
            getTurmas(){
                axios.get("{{route('turma-tutor.turmas')}}")
                    .then(response => {
                        response.data.map(item => {
                            this.tutorTurmas.push({
                                text: `${item.semestre_code} - ${item.disciplina_code} ${item.disciplina_name}`,
                                value: item.disciplina_id,
                            });
                        });
                    })
                    .catch(error => console.log(error.response.data))
            },
            getTurma(){
                axios.get("{{route('turma-tutor.problemas-unidade', request()->route()->id)}}")
                    .then(response => {
                        this.turma = response.data.turma;
                        this.novoProblemaLink = "{{route('tutor.adicionar-problema','disciplina_id')}}"
                            .replace('disciplina_id', response.data.turma.disciplina_id)
                        this.problemas = response.data.problemas;
                        this.todosAlunos = [];
                        this.todosAlunos = response.data.todosAlunos;
                        this.studentData = [];
                        response.data.alunos.map(aluno => {
                            this.studentData.push({
                                id: aluno.aluno_id,
                                name: `${aluno.first_name} ${aluno.surname}`,
                                enrollment: aluno.enrollment,
                            })
                        });
                        this.studentData = this.studentData.sort((a, b) => {
                            return a.name.localeCompare(b.name);
                        });

                        this.sessionData = response.data.sessoes;
                    })
                    .catch(error => console.log(error.response.data))
            },
            setDataToUpdate(item){
                this.dataToUpdate = item.problema_id;
                let splittedUrl = location.href.split('/');
                window.localStorage.setItem('tutorTurmaId', splittedUrl[splittedUrl.length - 1]);
                window.location.href = "{{route('tutor.editar-problema', 'id')}}".replace('id', this.dataToUpdate);
            },
            setDataToNote(problemaId){
                let splittedUrl = location.href.split('/');
                window.localStorage.setItem('tutorTurmaId', splittedUrl[splittedUrl.length - 1]);
                window.location.href = "{{route('tutor.problema-nota', 'problemaId')}}".replace('problemaId', problemaId);
            },
            setDataToCopy(item){
                this.dataToCopy = item.problema_id;
                this.copyDialog = true;
            },
            handleCopy(){
                if (this.selectedTutorTurma){
                    axios.post("{{route('problemas.copy')}}",
                    {
                        problema_id: this.dataToCopy,
                        disciplina_id: this.selectedTutorTurma
                    })
                    .then(response =>{
                        this.selectedTutorTurma = "";
                        this.dataToCopy = null;
                        this.copyDialog = false;
                        this.$refs.CopyProblema.reset();
                        this.getTurma();
                    })
                    .catch(error => {
                        console.log(error);
                        error.response.data.message.forEach((item) => {
                            this.handleError(item);
                        });
                    })
                }
            },

            formatDate (date) {
                if (!date) return null

                const [year, month, day] = date.split('-')
                return `${day}/${month}/${year}`
            },
            parseDate (date) {
                if (!date) return null
                const [day, month, year] = date.split('/')
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
            },
            handleError(error) {
                switch (error.field) {
                    case "name": {
                        this.errorMessages.name = error.message;
                        break;
                    }
                }
            },
            hide(type) {
                if (type == "store"){
                    this.stored = false;
                }else if (type == "update"){
                    this.updated = false;
                }else{
                    this.removed = false;
                }
            },
            cancelUpdate() {
                this.update = false;
            },
            cancelForm() {
                this.dialog = false;
                this.presencaDialog = false;
                this.matriculaDialog = false;
                if (this.tab == 2)
                    this.$refs.CopyProblema.reset();

                if (this.update == true) this.cancelUpdate();
                this.errorMessages.name = null;
            },
            formHandleSubmit(){
                this.waiting = true;
                this.form.title.trim();
                this.form.turma_id = "{{request('id')}}"
                if (this.$refs.addSessao.validate()) {
                    this.noItens = false;
                    axios.post("{{route('sessao.store')}}", this.form)
                        .then(response => {
                            this.waiting = false;
                            this.dialog = false;
                            this.stored = true;
                            this.form = { name: "", itens: [] };
                            this.$refs.addSessao.reset();
                            this.errorMessages.name = null;
                            this.getTurma();
                        })
                        .catch(error => {
                            console.log(error.response);
                            error.response.data.message.forEach(item => {
                                this.handleError(item);
                            })
                        });
                }
            },
            seeNotes(aluno){
                this.problemaNotas = [];
                let splittedUrl = location.href.split('/');
                let route = "{{route('problema-unidade.ver-nota', ['alunoId', 'disciplinaOfertadaId'])}}";
                route = route.replace('alunoId', aluno.id);
                route = route.replace('disciplinaOfertadaId', splittedUrl[splittedUrl.length - 1]);
                axios.get(route)
                    .then(response => {
                        response.data.problemas.map(problemaNota => {
                            this.problemaNotas.push({
                                problema: problemaNota.title,
                                notas: problemaNota.notaComPeso,
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
            matricularAlunos(){
                this.matriculaDialog = true;
            },

            formMatriculaHandleSubmit(){
                axios.post("{{route('turma-alunos.matricular', 'id')}}".replace('id', "{{request('id')}}"), this.matriculaForm)
                    .then(response => {
                        this.cancelForm();
                        this.getTurma();
                    })
                    .catch(error => {
                        console.log(error.response);
                        error.response.data.message.forEach(item => {
                            this.handleError(item);
                        })
                    })
            },
            //Presenca
            startPresenca(item){
                this.sessionId = item.id;
                this.presencaDialog = true;
                this.presencaForm.alunos = [];
                this.presencaForm.presenca = [];
                this.studentData.map(student =>{
                    this.presencaForm.alunos.push(student.id);
                    this.presencaForm.presenca.push(false);
                })
            },

            setAllTrue(){
                let checkboxes = document.getElementsByName('presencas');
                for(var i=0, n=checkboxes.length;i<n;i++) {
                    checkboxes[i].checked = true;
                }
                this.presencaForm.presenca.forEach((item, index) => {
                    this.presencaForm.presenca[index] = true;
                });
            },
            formPresencaHandleSubmit(){
                axios.post("{{route('presenca.store', 'sessionId')}}".replace('sessionId', this.sessionId), this.presencaForm)
                    .then(response => {
                        this.cancelForm();
                    })
                    .catch(error => {
                        console.log(error.response);
                        error.response.data.message.forEach(item => {
                            this.handleError(item);
                        })
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
            goToBarema(problemaId){
                let route ="{{route('tutor.barema', 'problemaId')}}".replace('problemaId', problemaId);
                window.location.href = route;
            }
        },
    });
</script>
@endpush