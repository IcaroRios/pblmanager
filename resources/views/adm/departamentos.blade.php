@php
    
@endphp

@extends('layout.main')

@push("header")

@endpush

@section("content")
@include('layout.navbar')
<v-app id="departamentos">
    <v-main>
        <v-container>
            <v-row id="departamento-table" class="mt-2">
              <v-col offset-md="2" md="8" sm="12">
                <v-card>
                  <v-card-title>
                    <h4>Departamentos</h4>
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
                      <v-col class="d-flex" cols="12" sm="8">
                        <v-text-field
                          align="center"
                          v-model="searchQuery"
                          append-icon="mdi-magnify"
                          label="Pesquisar..."
                          hide-details
                          dense
                        ></v-text-field>
                      </v-col>
                      <v-col class="d-flex justify-end" cols="12" sm="4">
                        <!-- New -->
                        @component('adm.modals.departamentos') @endcomponent
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
                    Departamento removido com sucesso!
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
    var departamentos = new Vue({
        el: '#departamentos',
        vuetify: new Vuetify(),
        data: {
            data: undefined,
            searchQuery: "",
            headers: [],
            waiting: false,
            dataToUpdate: null,
            removed: false,

            //TABLE VARS
            confirmDialog: false,
            selectedItem: undefined,

            //FORM VARS
            form: { abbreviation: "", name: "" },
            dialog: false,
            validForm: undefined,
            abbreviatonRules: [
                (v) => !!v || "Sigla do Departamento é um campo obrigatório",
                (v) =>
                /[D][A-Z]{3}/.test(v) ||
                "Sigla deve começar com 'D' e conter apenas letras",
            ],
            nameRules: [
                (v) => !!v || "Nome do Departamento é um campo obrigatório",
                (v) =>
                /([A-Z] || [a-z])+/.test(v) ||
                "Nome do departamento deve conter apenas letras",
            ],
            stored: false,
            update: false,
            updated: false,
            errorMessages: { abbreviation: null },
        },
        mounted(){
            this.componentStructure();
        },
        computed: {
            filteredList() {
                if (this.searchQuery != "") {
                    return this.filter(this.data, this.searchQuery);
                } else {
                    return this.data;
                }
            },

            notFound() {
                if (this.data == undefined || this.data.length == 0) {
                    return "Ainda não foram cadastrados departamentos";
                } else {
                    return "Nenhum departamento encontrado";
                }
            },

            snackText() {
                return this.updated == false
                    ? "Departamento Adicionado com Sucesso!"
                    : "Departamento Atualizado com Sucesso!";
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
                { text: "Sigla", value: "abbreviation", width: "12%" },
                { text: "Nome", value: "name", width: "55%" },
                { text: "Actions", value: "action", sortable: false }
            ];
            this.getDepartamentos();
          },
          getDepartamentos(){
            axios.get("{{route('departamentos.index')}}")
                .then(response => {
                    this.data = response.data.sort((a, b) => {
                        return a.abbreviation.localeCompare(b.abbreviation);
                    });
                })
                .catch(error => console.log(error))
          },
          handleUpdate(item) {
            this.dataToUpdate = item.id;
            this.form = {...item};
            this.dialog = true;
            this.update = true;
          },
          handleDelete() {
            this.waiting = true;
            axios.delete("{{route('departamentos.destroy', 'id')}}".replace('id', this.selectedItem.id))
              .then(response => {
                this.waiting = false;
                this.confirmDialog = false;
                this.removed = true;
                this.getDepartamentos();
              })
              .catch(error => {
                console.log(error);
              })
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
            this.form.abbreviation.trim();
            this.form.name.trim();
            if (this.$refs.addDepartamento.validate()) {
              axios.post("{{route('departamentos.store')}}", this.form)
                .then(response => {
                  this.dialog = false;
                  this.stored = true;
                  this.form = { abbreviation: "", name: "" };
                  this.$refs.addDepartamento.reset();
                  this.errorMessages.abbreviaton = null;
                  this.getDepartamentos();
                })
                .catch(error => {
                  console.log(error.response);
                  error.response.data.message.forEach((item) => {
                    this.handleError(item);
                  })
                });
            }
          },
          formHandleUpdate() {
            if (this.$refs.addDepartamento.validate()) {
              axios.put("{{route('departamentos.update', 'id')}}".replace('id', this.dataToUpdate), this.form)
                .then(response => {
                  this.dialog = false;
                  this.updated = true;
                  this.update = false;
                  this.form = { abbreviation: "", name: "" };
                  this.$refs.addDepartamento.reset();
                  this.dataToUpdate = null;
                  this.getDepartamentos();
                })
                .catch(error => {
                  console.log(error.response);
                  error.response.data.message.forEach((item) => {
                    this.handleError(item);
                  });
                })
            }
          },
          closeModal(){
            form = {};
            this.errorMessages.abbreviation = null;
            this.$refs.addDepartamento.reset();
            if (this.update == true) this.cancelUpdate();
            this.dialog = false;
          },
          cancelUpdate() {
            this.update = false;
            this.dataToUpdate = null;
          },
          handleError(error) {
            switch (error.field) {
              case "abbreviation": {
                this.errorMessages.abbreviation = error.message;
                break;
              }
            }
          },
        }
    });
</script>
@endpush