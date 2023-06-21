@extends('layout.main')

@push("header")

@endpush

@section("content")
@include('layout.navbar')
<v-app id="tutores">
    <v-main>
        <v-container>
            <v-row id="tutor-table" class="mt-2">
                <v-col offset-md="2" md="8" sm="12">
                  <v-card>
                    <v-card-title>
                      <h4>Tutores</h4>
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
                            label="Pesquisar nome..."
                            hide-details
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col class="d-flex justify-end" cols="12" sm="4">
                        @component('adm.modals.adicionar-tutor') @endcomponent
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
                    </v-data-table>
                </div>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </template>
    </v-app>
</v-main>
@endsection

@push("scripts")
<script>
    var tutores = new Vue({
        el: '#tutores',
        vuetify: new Vuetify(),
        data: {
            data: undefined,
            searchQuery: "",
            headers: [],
            waiting: false,

            //Tutor FORM VARS
            formTutor: {
                email: "",
                password: "",
                password_confirmation: "",
                fullName: "",
                username: ""
            },
            dialogTutor: false,
            validTutorForm: undefined,
            stored: false,
            error: false,
            snackTextTutor: null,

            //TUTOR FORM VARS
            dialogTutor: false,
            tutores: [],
            snackTextTutor: undefined,
            waitingTutor: false,
            nameRules: [(v) => !!v || "Nome é requerido"],
            emailRules: [(v) => !!v || "Email é requerido",
                         (v) => /.+@.+\..+/.test(v) || "Email deve ser válido"],
            passwordRules: [(v) => !!v || "Insira uma senha"],
            usernameRules: [(v) => !!v || "Insira um nome de usuário"],
        },
        mounted(){
            this.headers = [
                    { text: "Id", value: "id" },
                    { text: "Nome", value: "name" },
                    { text: "email", value: "email"},
                    { text: "username", value: "username"},
                ];
            this.getTutores();
        },
        computed: {
            filteredList() {
                let search = this.removeSpecial(this.searchQuery.toLowerCase().trim());
                var itens = [];
                itens = this.data;
                if (search != "") {
                    return itens.filter((item) => {
                        return (
                            this.removeSpecial(item.first_name.toLowerCase()).includes(search) ||
                            this.removeSpecial(item.surname.toLowerCase()).includes(search) ||
                            this.removeSpecial(item.email.toLowerCase()).includes(search)
                        );
                    });
                } else {
                    return itens;
                }
            },
            notFound() {
                if (this.data == undefined || this.data.length == 0) {
                    return "Ainda não foram cadastrados tutores";
                } else {
                    return "Nenhum tutor encontrado";
                }
            },
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
            getTutores(){
                this.data = [];
                axios.get("{{route('users.tipo', 'typeId')}}".replace('typeId', 2))
                    .then(response => {
                        response.data.map((item) => {
                            item.name = item.first_name + " " + item.surname
                            this.data.push(item);
                        });
                    })
                    .catch(error => console.log(error))
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
                }else if (type == "error"){
                    this.error = false;
                }else{
                    this.removed = false;
                }
            },
            successTextTutor() {
                this.snackTextTutor = "Tutor Criado com Sucesso!"
            },
            formTutorHandleSubmit() {
                if (this.$refs.addTutor.validate()) {
                    this.waiting = true;
                    axios.post("{{route('users-tutor.store')}}", this.formTutor)
                        .then(response => {
                            var disciplina = response.data;
                            var i = 0;
                            this.waiting = false;
                            this.stored = true;
                            this.dialogTutor = false;
                            this.formTutor = {};
                            this.$refs.addTutor.reset();
                            this.getTutores();
                            this.successTextTutor();
                        })
                    .catch(error => {
                        this.waiting = false;
                        this.dialogTutor = false;
                        this.error = true;
                        this.snackTextTutor = Object.entries(error.response.data.errors)[0][1][0]
                    });
                }
            },
            closeTutorModal(){
                formTutor = {};
                this.$refs.addTutor.reset();
                this.dialogTutor = false;
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