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
<v-app id="problemas">
    <v-main>
        <v-container>
            <v-row id="problema-table" class="mt-2">
                <v-col offset-md="2" md="8" sm="12">
                  <v-card>
                    <v-card-title>
                      <h4>Repositório de Problemas</h4>
                    </v-card-title>
                    <v-card-subtitle>
                      <v-spacer></v-spacer>
                      <v-row no-gutter align="center">
                        <v-col class="d-flex" cols="12" sm="5">
                          <v-text-field
                            align="center"
                            v-model="searchQuery"
                            append-icon="mdi-magnify"
                            label="Pesquisar..."
                            hide-details
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="3">
                          <v-select
                            dense
                            style="margin-top: 22px !important; "
                            :items="turmas"
                            label="Filtrar"
                            v-model="filterQuery"
                            append-icon="mdi-filter"
                            clearable
                          ></v-select>
                        </v-col>
                        <v-col class="d-flex justify-end" cols="12" sm="4">
                        </v-col>
                    </v-row>
                  </v-card-subtitle>
                  <!-- New -->
                  <div>
                    <v-data-table
                      :loading="data == undefined"
                      :items="filteredList"
                      :headers="headers"
                      loading-text="Carregando... Por favor, aguarde"
                      :items-per-page="10"
                      class="elevation-1 row-pointer table"
                      :no-data-text="notFound"
                    >
                      <template v-slot:item.action="{ item }">
                        <v-btn
                          @click="handleUpdate(item)"
                          elevation="2"
                          small
                          color="warning"
                          tile
                        >
                          <v-icon left> mdi-pencil </v-icon>Editar</v-btn
                        >
                        <v-btn
                            @click="setDataToCopy(item)"
                            elevation="2"
                            small
                            color="info"
                            tile
                            class="ml-2"
                        >
                            <v-icon left> mdi-file </v-icon>Copiar
                        </v-btn>
                      </template>
                    </v-data-table>
                  </div>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
                <v-col class="d-flex justify-end" cols="12" sm="12">
                    @component('tutor.modals.copiar-problema') @endcomponent
                </v-col>
            </v-row>
        </v-container>
    </v-app>
</v-main>
@endsection

@push("scripts")
<script>
    var problemas = new Vue({
        el: '#problemas',
        vuetify: new Vuetify(),
        data: {
            turmas: [],
            headers: [],
            tutorTurmas: [],
            waiting: false,
            searchQuery: "",
            filterQuery: null,
            data: undefined,

            dataToCopy: undefined,
            selectedTutorTurma: "",
            validForm: undefined,
            //TABLE VARS
            dialog: false,
            confirmDialog: false,
            selectedItem: undefined,
        },

        computed: {
            filteredList() {
              let search = this.removeSpecial(this.searchQuery.toLowerCase().trim());
              var itens = [];
              if (this.filterQuery == null) {
                itens = this.data;
              } else {
                itens = this.data.filter((item) => {
                    return item.disciplina_id == this.filterQuery;
                });
              }
              if (search != "") {
                  return itens.filter((item) => {
                  return (
                      this.removeSpecial(item.title.toLowerCase()).includes(search) ||
                      this.removeSpecial(item.disciplina_name.toLowerCase()).includes(search) || 
                      this.removeSpecial(item.semestre_code.toLowerCase()).includes(search)
                  );
                  });
              } else {
                return itens;
              }
          },

          notFound() {
              if (this.data == undefined || this.data.length == 0) {
                  return "Ainda não foram cadastrados problemas";
              } else {
                  return "Nenhum problema encontrado";
              }
          },
        },

        created(){
            this.getTurmas();
            this.componentStructure();
        },

        methods: {
            getTurmas(){
                axios.get("{{route('turma-tutor.turmas')}}")
                    .then(response => {
                        response.data.map(item => {
                            this.turmas.push({
                                text: `${item.semestre_code} - ${item.disciplina_code} ${item.disciplina_name}`,
                                value: item.disciplina_id,
                            });
                        });
                        this.tutorTurmas = this.turmas;
                    })
                    .catch(error => console.log(error.response.data))
            },
            getProblemasUnidades(){
                axios.get("{{route('turma-tutor.problemas')}}")
                    .then(response => {
                        this.data = response.data.sort((a, b) => {
                            return a.title.localeCompare(b.title);
                        });
                    })
                    .catch(error => console.log(error.response.data))
            },
            componentStructure() {
                this.headers = [
                    { text: "Titulo", value: "title"},
                    { text: "Semestre", value: "semestre_code"},
                    { text: "Disciplina", value: "disciplina_name"},
                    { text: "Actions", value: "action", sortable: false }
                ];
                this.getProblemasUnidades();
            },
            removeSpecial(texto) {
                texto = texto.replace(/[ÀÁÂÃÄÅ]/, "A");
                texto = texto.replace(/[àáâãäå]/, "a");
                texto = texto.replace(/[ÈÉÊË]/, "E");
                texto = texto.replace(/[Ç]/, "C");
                texto = texto.replace(/[ç]/, "c");
                return texto;
            },
            handleUpdate(item){
                window.localStorage.setItem('tutorTurmaId', item.disciplina_id);
                window.location.href = "{{route('tutor.editar-problema', 'id')}}".replace('id', item.problema_id);
            },
            setDataToCopy(item){
                this.dataToCopy = item.problema_id;
                this.dialog = true;
            },
            cancelForm() {
                this.dialog = false;
                this.$refs.CopyProblema.reset();
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
                        this.dialog = false;
                        this.$refs.CopyProblema.reset();
                        this.getProblemasUnidades();
                    })
                    .catch(error => {
                        console.log(error);
                        error.response.data.message.forEach((item) => {
                            this.handleError(item);
                        });
                    })
                }
            },
        }
    });
</script>
@endpush