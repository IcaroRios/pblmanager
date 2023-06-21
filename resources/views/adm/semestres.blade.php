
@extends('layout.main')

@push("header")

@endpush

@section("content")
@include('layout.navbar')
<v-app id="semestres">
    <v-main>
        <v-container>
            <v-row id="disciplina-table" class="mt-2">
                <v-col offset-md="2" md="8" sm="12">
                  <v-card>
                    <v-card-title>
                      <h4>Semestres</h4>
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
                        <v-col class="d-flex justify-end" cols="12" sm="4">
                            <!-- New -->
                            @component('adm.modals.semestres') @endcomponent
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
                    Semestre removido com sucesso!
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
    var semestres = new Vue({
        el: '#semestres',
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
            form: { code: "", start_date: "", end_date: "" },
            formattedStartDate: "",
            formattedEndDate: "",
            dialog: false,
            validForm: undefined,
            codeRules: [
                (v) => !!v || "Código da Disciplina é um campo obrigatório",
                (v) =>
                /\b[2][0][0-9]{2}[.][1-3]\b/g.test(v) ||
                "Código do Semestre deve seguir o formato NNNN.N",
            ],
            dateRules: [(v) => !!v || "Campo de Data Obrigatório"],
            stored: false,
            update: false,
            updated: false,
            menu: false,
            menu2: false,
            errorMessages: { code: null },
        },
        mounted(){
            this.componentStructure();
        },
        computed: {
            filteredList() {
                let search = this.removeSpecial(this.searchQuery.toLowerCase().trim());
                if (search != "") {
                    return this.data.filter((item) => {
                    return this.removeSpecial(item.code.toLowerCase()).includes(search);
                    });
                } else {
                    return this.data;
                }
            },

            notFound() {
                if (this.data == undefined || this.data.length == 0) {
                    return "Ainda não foram cadastrados semestres";
                } else {
                    return "Nenhum semestre encontrado";
                }
            },

            currentDate() {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, "0");
                var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
                var yyyy = today.getFullYear();
                today = yyyy + "-" + mm + "-" + dd;
                return today;
            },
                // Fazer tratamento de mês
            minEndDate() {
                if (this.form.start_date) {
                    var min = this.form.start_date;
                    min = min.split("-");
                    min[2] = parseInt(min[2]) + 1;
                    if (min[2] < 10) {
                    min[2] = `0${min[2]}`;
                    }
                    min = min[0] + "-" + min[1] + "-" + min[2];
                    return min;
                } else {
                    return this.currentDate;
                }
            },
            snackText() {
                return this.updated == false
                    ? "Semestre Adicionado com Sucesso!"
                    : "Semestre Atualizado com Sucesso!";
            },
        },
        watch:{
            "form.start_date" (val) {
                this.formattedStartDate = this.formatDate(this.form.start_date)
            },
            "form.end_date" (val) {
                this.formattedEndDate = this.formatDate(this.form.end_date)
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
                    { text: "Código", value: "code" },
                    { text: "Data de Início", value: "start_date" },
                    { text: "Data de Termino", value: "end_date" },
                    { text: "Actions", value: "action", sortable: false, width: "33%" }
                ];
                this.getSemestres();
            },
            getSemestres(){
                axios.get("{{route('semestres.index')}}")
                    .then(response => {
                            let items = response.data.sort((a, b) => {
                                return b.code.localeCompare(a.code);
                            });
                            this.data = [];
                            items.map((item) => {
                                item.start_date = this.formatDate(item.start_date);
                                item.end_date = this.formatDate(item.end_date);
                                this.data.push(item);
                            });
                        })
                    .catch(error => console.log(error));
            },
            handleUpdate(item) {
                this.dataToUpdate = item.id;
                this.form = {...item};
                this.form.start_date = this.parseDate(this.form.start_date);
                this.form.end_date = this.parseDate(this.form.end_date);
                this.dialog = true;
                this.update = true;
            },
            handleDelete() {
                this.waiting = true;
                axios.delete("{{route('semestres.destroy', 'id')}}".replace('id', this.selectedItem.id))
                    .then(response => {
                        this.waiting = false;
                        this.removed = true;
                        this.confirmDialog = false;
                        this.waiting = false;
                        this.getSemestres();
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            formatDate (date) {
                if (!date) return null

                const [year, month, day] = date.split('-')
                return `${day}-${month}-${year}`
            },
            parseDate (date) {
                if (!date) return null

                const [day, month, year] = date.split('-')
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
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
                if (this.form.code) this.form.code.trim();
                if (this.form.start_date) this.form.start_date.trim();
                if (this.form.end_date) this.form.end_date.trim();
                if (this.$refs.addSemestre.validate()) {
                    axios.post("{{route('semestres.store')}}", this.form)
                        .then(response => {
                            this.dialog = false;
                            this.stored = true;
                            this.form = { code: "", start_date: "", end_date: "" };
                            this.$refs.addSemestre.reset();
                            this.errorMessages.code = null;
                            this.getSemestres();
                        })
                        .catch(error => {
                            this.errorMessages.code = error.response.data.message
                        })
                }
            },
            formHandleUpdate() {
                if (this.$refs.addSemestre.validate()) {
                    axios.put("{{route('semestres.update', 'id')}}".replace('id', this.dataToUpdate), this.form)
                        .then(response => {
                            this.dialog = false;
                            this.updated = true;
                            this.update = false;
                            this.form = {};
                            this.$refs.addSemestre.reset();
                            this.dataToUpdate = null;
                            this.waiting = false;
                            this.getSemestres();
                        })
                        .catch(error => {
                            console.log(error.response);
                            error.response.data.message.forEach((item) => {
                                this.handleError(item);
                            });
                        });
                }
            },
            closeModal(){
                form = {};
                this.$refs.addSemestre.reset();
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