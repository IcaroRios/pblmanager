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
<v-app id="nota">
<v-main id="nota">
    <v-container>
      <v-row id="turma-title" class="mt-2">
        <v-col offset-md="12">
          <v-card >
              <v-card-title>
                  <h5>Atribuir nota</h5>
                  <v-spacer></v-spacer>
                  <v-btn 
                    style="background-color: var(--primary-dark-color); color: white" 
                    @click="back"
                  >
                    Voltar
                </v-btn>
              </v-card-title>
              <v-card-subtitle >
                <v-form v-model="validForm" ref="AddNota">
                    <v-row>
                        <v-col cols="12" class="mt-2">
                            <v-select
                              dense
                              :items="alunos"
                              label="Aluno"
                              v-model="form.aluno_id"
                              clearable
                              :error="errorMessages.aluno != null"
                              :error-messages="errorMessages.aluno"
                            ></v-select>
                        </v-col>

                        <v-col cols="12" class="mt-2">
                            <v-select
                              dense
                              :items="baremaSelect"
                              label="Barema"
                              v-model="form.barema_id"
                              @change="selectItens"
                              clearable
                            ></v-select>
                        </v-col>

                        <v-col cols="12" class="mt-2" v-for="itemBarema in selectedItens">
                            <v-text-field
                                :label="itemBarema.name"
                                name="baremaNota"
                                type="number"
                                min="0"
                                max="10"
                                step="0.01"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>
                </v-form>
                <div class="text-right mt-5">
                    <v-btn style="background-color: var(--primary-dark-color); color: white" @click.prevent="handleSubmit()">
                        Adicionar
                    </v-btn>
                </div>
            </v-card-subtitle>
        </v-card>
        </v-col>
      </v-row>
      <v-snackbar v-model="stored" color="success" right bottom>
        <h6 style="margin: 0px !important">
            Nota aplicada com sucesso
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide()">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
    </v-container>
</v-main>
</v-app>
@endsection

@push("scripts")
<script>

    var nota = new Vue({
        el: '#nota',
        vuetify: new Vuetify(),
        data: {
            baremas: [],
            alunos: [],
            baremaSelect: [],
            validForm: undefined,
            selectedItens: [],
            stored: false,

            form:{
                aluno_id: '',
                barema_id: '',
                feedback: '',
            },

            errorMessages: { aluno: null },
        },

        created(){
            this.getData();
        },

        methods: {
            back(){
                let route = "{{route('tutor.turma', 'id')}}".replace('id', window.localStorage.getItem('tutorTurmaId'));
                window.location.href = route;
            },
            getData(){
                this.getBaremas();
                this.getAlunos();
            },
            getBaremas(){
                let splittedUrl = location.href.split('/');
                let problemaId = splittedUrl[splittedUrl.length - 1];
                axios.get(`{{route('baremas.index')}}?problema=${problemaId}`)
                    .then(response => {
                        this.baremas = response.data;
                        response.data.map(barema => {
                            this.baremaSelect.push({
                                value: barema.id,
                                text: barema.name
                            })
                        });
                    })
                    .catch(error => console.log(error))
            },
            getAlunos(){
                axios.get("{{route('turma-alunos.por-turma', 'turmaId')}}".replace('turmaId',  window.localStorage.getItem('tutorTurmaId')))
                    .then(response => {
                        response.data.map(aluno => {
                            this.alunos.push({
                                value: aluno.aluno_id,
                                text: `${aluno.first_name} ${aluno.surname} - ${aluno.enrollment}`,
                            })
                        });
                    })
                    .catch(error => console.log(error))
            },
            selectItens(){
                let barema = this.baremas.find(barema => barema.id == this.form.barema_id);
                this.selectedItens = barema.item_baremas;
            },
            handleSubmit(){
                let notaValues = document.getElementsByName('baremaNota');
                let notasJson = "";
                for(var i=0, n = notaValues.length; i < n; i++) {
                    notasJson += `"${this.selectedItens[i].name}": ${notaValues[i].value}`
                    if (i < notaValues.length - 1)
                        notasJson += ",";
                }
                this.form.feedback = `{${notasJson}}`;

                let disciplinaOfertadaId = window.localStorage.getItem('tutorTurmaId');
                let splittedUrl = location.href.split('/');
                let problemaId = splittedUrl[splittedUrl.length - 1];

                let route = "{{route('problema-unidade.aplicar-nota', ['problemaId', 'disciplinaOfertadaId'])}}";
                route = route.replace('problemaId', problemaId);
                route = route.replace('disciplinaOfertadaId', disciplinaOfertadaId);

                if (this.$refs.AddNota.validate()) {
                    axios.post(route, this.form)
                        .then(response => {
                            this.stored = true;
                            this.form = { aluno_id: "", barema_id: "", feedback: ""};
                            this.selectedItens = [];
                            this.$refs.AddNota.reset();
                            this.errorMessages.aluno = null;
                        })
                        .catch(error => {
                            console.log(error);
                            this.errorMessages.aluno = error.response.data.message;
                        });
                }
            },
            hide() {
                this.stored = false;
            },
        },
    });
</script>
@endpush