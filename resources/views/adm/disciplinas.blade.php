@php
    
@endphp

@extends('layout.main')

@push("header")

@endpush

@section("content")
@include('layout.navbar')
<v-app id="disciplinas">
    <v-main>
        <v-container>
            <v-row id="disciplina-table" class="mt-2">
                <v-col offset-md="2" md="8" sm="12">
                  <v-card>
                    <v-card-title>
                      <h4>Disciplinas</h4>
                    <v-spacer></v-spacer>
                    <v-btn
                    color="primary"
                    style="background-color: var(--primary-dark-color)"
                    href="{{route('adm.menu')}}"
                    >
                        Voltar
                    </v-btn>
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
                            :items="departamentos"
                            label="Filtrar"
                            v-model="filterQuery"
                            append-icon="mdi-filter"
                            clearable
                          ></v-select>
                        </v-col>
                        <v-col class="d-flex justify-end" cols="12" sm="4">
                          <!-- New -->
                          @component('adm.modals.disciplinas') @endcomponent
                        </v-col>
                    </v-row>
                  </v-card-subtitle>
                  <!-- New -->
                  @component('layout.table')
                  @endcomponent
                </v-card>
              </v-col>
            </v-row>
            <v-snackbar v-model="removed" color="success" right bottom>
                <h6 style="margin: 0px !important">
                    Disciplina removida com sucesso!
                </h6>
                <template v-slot:action="{ attrs }">
                    <v-btn color="white" text v-bind="attrs" @click="hide('remove')">
                        Fechar
                    </v-btn>
                </template>
            </v-snackbar>
          </v-container>
        </template>
    </v-app>
</v-main>
@endsection

@push("scripts")
<script>
    var disciplinas = new Vue({
        el: '#disciplinas',
        vuetify: new Vuetify(),
        data: {
          data: undefined,
          searchQuery: "",
          filterQuery: null,
          headers: [],
          waiting: false,
          dataToUpdate: null,
          removed: false,

          //TABLE VARS
          confirmDialog: false,
          selectedItem: undefined,

          //FORM VARS
          form: { code: "", name: "", workload: "", departamento_id: "" },
          workload: [30, 45, 60, 90],
          departamentos: [],
          dialog: false,
          validForm: undefined,
          codeRules: [
              (v) => !!v || "Código da Disciplina é um campo obrigatório",
              (v) =>
              /\b[A-Z]{3}[0-9]{3}\b/g.test(v) ||
              "Código da Disciplina deve conter 3 letras e 3 números",
          ],
          nameRules: [
              (v) => !!v || "Nome da Disciplina é um campo obrigatório",
              (v) =>
              /([A-Z] || [a-z])+/.test(v) ||
              "Nome da Disciplina deve conter apenas letras",
          ],
          workloadRules: [
              (v) => !!v || "Carga Horária é um campo obrigatório",
              (v) =>
              /([30] || [60])/.test(v) || "A carga horária deve ser 30 ou 60 horas",
          ],
          departamentoRules: [
              (v) => !!v || "Departamento é um campo obrigatório",
              (v) => /([0-9]+)/.test(v),
          ],
          stored: false,
          update: false,
          updated: false,
          errorMessages: { code: null },
      },
      mounted(){
          this.componentStructure();
      },
      computed: {
          filteredList() {
              let search = this.removeSpecial(this.searchQuery.toLowerCase().trim());
              var itens = [];
              if (this.filterQuery == null) {
                  itens = this.data;
              } else {
                  itens = this.data.filter((item) => {
                  console.log(item.data);
                  return item.departamento_id == this.filterQuery;
                  });
              }
              if (search != "") {
                  return itens.filter((item) => {
                  return (
                      this.removeSpecial(item.code.toLowerCase()).includes(search) ||
                      this.removeSpecial(item.name.toLowerCase()).includes(search)
                  );
                  });
              } else {
                  return itens;
              }
          },

          notFound() {
              if (this.data == undefined || this.data.length == 0) {
                  return "Ainda não foram cadastradas disciplinas";
              } else {
                  return "Nenhuma disciplina encontrada";
              }
          },

          snackText() {
              return this.updated == false
                  ? "Disciplina Adicionada com Sucesso!"
                  : "Disciplina Atualizada com Sucesso!";
              },
      },
      methods: {
        filter(list, searchQuery) {
          let search = this.removeSpecial(this.searchQuery.toLowerCase().trim());
          if (search != "") {
              return list.filter((item) => {
                  return (
                      this.removeSpecial(item.name.toLowerCase()).includes(search) ||
                      this.removeSpecial(item.abbreviation.toLowerCase()).includes(search)
                  );
              });
          } else {
              return list;
          }
        },
        removeSpecial(texto) {
          texto = texto.replace(/[ÀÁÂÃÄÅ]/, "A");
          texto = texto.replace(/[àáâãäå]/, "a");
          texto = texto.replace(/[ÈÉÊË]/, "E");
          texto = texto.replace(/[Ç]/, "C");
          texto = texto.replace(/[ç]/, "c");
          return texto;
        },
        componentStructure() {
          this.headers = [
              { text: "Código", value: "code", width: "12%" },
              { text: "Disciplina", value: "name"},
              { text: "Actions", value: "action", sortable: false }
          ];
          this.getDisciplinas();
          this.getDepartamentos();
        },
        getDepartamentos(){
          axios.get("{{route('departamentos.index')}}")
            .then(response => {
              console.log(response.data);
              response.data.map((item) => {
                this.departamentos.push({
                    text: item.abbreviation,
                    value: item.id,
                });
              });
            })
            .catch(error => console.log(error));
        },
        getDisciplinas(){
          axios.get("{{route('disciplinas.index')}}")
            .then(response => {
              this.data = response.data.sort((a, b) => {
                  return a.code.localeCompare(b.code);
              });
            })
            .catch(error => console.log(error));
        },
        handleUpdate(item) {
          this.dataToUpdate = item.id;
          this.form = {...item};
          this.form.departamento_id = item.departamento_id;
          this.dialog = true;
          this.update = true;
        },
        handleDelete() {
          this.waiting = true;
          axios.delete("{{route('disciplinas.destroy', 'id')}}".replace('id', this.selectedItem.id))
            .then(response => {
              this.waiting = false;
              this.removed = true;
              this.confirmDialog = false;
              this.getDisciplinas();
            })
            .catch(error => {
              console.log(error);
            });
        },
        removeSpecial(texto) {
          texto = texto.replace(/[ÀÁÂÃÄÅ]/, "A");
          texto = texto.replace(/[àáâãäå]/, "a");
          texto = texto.replace(/[ÈÉÊË]/, "E");
          texto = texto.replace(/[Ç]/, "C");
          texto = texto.replace(/[ç]/, "c");
          return texto;
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

        //FORM METHODS
        formHandleSubmit() {
          this.waiting = true;
          this.form.code.trim();
          this.form.name.trim();
          axios.post("{{route('disciplinas.store')}}", this.form)
            .then(response =>{
              this.dialog = false;
              this.stored = true;
              this.form = {};
              this.$refs.addDisciplina.reset();
              this.errorMessages.code = null;
              this.waiting = false;
              this.getDisciplinas();
            })
            .catch(error =>{
              console.log(error.response);
              error.response.data.message.forEach((item) => {
                this.handleError(item);
              });
            })
        },
        formHandleUpdate() {
          axios.put("{{route('disciplinas.update', 'id')}}".replace('id', this.dataToUpdate), this.form)
            .then(response => {
              this.dialog = false;
              this.updated = true;
              this.update = false;
              this.form = {};
              this.$refs.addDisciplina.reset();
              this.getDisciplinas();
              this.dataToUpdate = null;
            })
            .catch(error => {
              console.log(error);
              error.response.data.message.forEach((item) => {
                this.handleError(item);
              });
            });
        },
        closeModal(){
          form = {};
          this.$refs.addDisciplina.reset();
          if (this.update == true) this.cancelUpdate();
          this.errorMessages.code = null;
          this.dialog = false;
        },
        cancelUpdate() {
          this.update = false;
          this.dataToUpdate = null;
        },
        handleError(error) {
          switch (error.field) {
            case "code": {
              this.errorMessages.code = error.message;
              break;
            }
          }
        },
      }
    });
</script>
@endpush