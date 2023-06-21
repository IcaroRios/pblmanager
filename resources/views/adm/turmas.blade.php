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
                      <h4>Turmas</h4>
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
                              :items="semestres"
                              label="Filtrar"
                              v-model="filterQuery"
                              append-icon="mdi-filter"
                              clearable
                            ></v-select>
                          </v-col>
                        <v-col class="d-flex justify-end" cols="12" sm="4">
                            <!-- New -->
                            @component('adm.modals.turmas') @endcomponent
                            @component('adm.modals.tutor') @endcomponent
                        </v-col>
                    </v-row>
                  </v-card-subtitle>
                  <!-- New -->
                  @component('layout.table') @endcomponent
                </v-card>
              </v-col>
            </v-row>
            <v-snackbar v-model="removed" color="success" right bottom>
                <h6 style="margin: 0px !important">
                    Turma removida com sucesso!
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
            semestres: [],
            searchQuery: "",
            filterQuery: null,
            headers: [],
            waiting: false,
            turma: undefined,
            removed: false,

            //TABLE VARS
            confirmDialog: false,
            selectedItem: undefined,

            //TURMA FORM VARS
            formTurma: {
                disciplina_id: null,
                semestre_id: null,
                number_of_classes: null,
            },
            classes: {
                days: [],
                time: [],
            },
            days: [
                { text: "Segunda", value: "seg" },
                { text: "Terça", value: "ter" },
                { text: "Quarta", value: "qua" },
                { text: "Quinta", value: "qui" },
                { text: "Sexta", value: "sex" },
            ],
            times: [
                { text: "7:30 - 9:30", value: "7:30 - 9:30" },
                { text: "9:30 - 11:30", value: "9:30 - 11:30" },
                { text: "13:30 - 15:30", value: "13:30 - 15:30" },
                { text: "15:30 - 17:30", value: "15:30 - 17:30" },
            ],
            dialogTurma: false,
            validTurmaForm: undefined,
            disciplinaRules: [(v) => !!v || "Disciplina é um campo obrigatório"],
            semestreRules: [(v) => !!v || "Semestre é um campo obrigatório"],
            numberRules: [
                (v) => !!v || "Número de turmas é um campo obrigatório",
                (v) => v <= 10 || "Número máximo de 10 turmas",
                (v) => v > 0 || "Número de turmas deve ser positivo",
            ],
            stored: false,
            error: false,
            disciplinas: [],
            semestres: [],
            snackTextTurma: null,

            //TUTOR FORM VARS
            selectedTutores: [],
            alocados: [],
            dialogTutor: false,
            tutores: [],
            turma_tutor: [],
            snackTextTutor: undefined,
            waitingTutor: false,
        },
        mounted(){
            this.componentStructure();
            this.getDisciplinas();
            this.getSemestres();
        },
        computed: {
            filteredList() {
                let search = this.removeSpecial(this.searchQuery.toLowerCase().trim());
                var itens = [];
                if (this.filterQuery == null) {
                    itens = this.data;
                } else {
                    itens = this.data.filter((item) => {
                        return item.semestre_id == this.filterQuery;
                    });
                }
                if (search != "") {
                    return itens.filter((item) => {
                    return (
                        this.removeSpecial(item.code.toLowerCase()).includes(search) ||
                        this.removeSpecial(item.disciplina_name.toLowerCase()).includes(
                        search
                        )
                    );
                    });
                } else {
                    return itens;
                }
            },
            notFound() {
                if (this.data == undefined || this.data.length == 0) {
                    return "Ainda não foram cadastradas turmas";
                } else {
                    return "Nenhuma turma encontrada";
                }
            },

            correctWorkload() {
                if (this.formTurma.disciplina_id != null && this.classes.days.length > 0) {
                    var selected = this.disciplinas.filter((item) => {
                    return this.formTurma.disciplina_id == item.value;
                    });
                    return selected[0].carga / 30 != this.classes.days.length;
                } else return false;
            },
            correctTime() {
                if (this.formTurma.disciplina_id != null) {
                    var selected = this.disciplinas.filter((item) => {
                    return this.formTurma.disciplina_id == item.value;
                    });

                    return selected[0].carga / 30 < this.classes.time.length;
                } else return false;
            },

            selectableTutores() {
                var tutores = this.tutores;
                this.alocados.map((selected) => {
                    tutores = tutores.filter((item) => {
                    return item.value != selected.value;
                    });
                });
                return tutores;
            },
        },
        watch:{
            dataToUpdate() {
                if (this.dataToUpdate != null) this.getOne();
            },

            turma() {
                if (this.turma != null) {
                    this.getTutores();
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
            componentStructure() {
                this.headers = [
                    { text: "Código", value: "code", width: "12%" },
                    { text: "Disciplina", value: "disciplina_name"},
                    { text: "Semestre", value: "semestre_code", width: "14%" },
                    { text: "Actions", value: "action2", sortable: false, width: "34%"}
                ];
                this.getTurmas();
            },
            getTurmas(){
                axios.get("{{route('turmas.index')}}")
                    .then(response => {
                        let items = response.data;
                        this.data = [];
                        items.map((item) => {
                            item.code = `${item.disciplina_code}-${item.code}`;
                            this.data.push(item);
                        });
                        this.data = this.data.sort((a, b) => {
                            if (a.disciplina_ofertada_id == b.disciplina_ofertada_id) {
                                return a.code.localeCompare(b.code);
                            } else {
                                return a.disciplina_code.localeCompare(b.disciplina_code);
                            }
                        });
                    })
                    .catch(error => console.log(error));
            },
            formatDate(date) {
                var newDate = date.split("-");
                newDate = newDate[2] + "-" + newDate[1] + "-" + newDate[0];
                return newDate;
            },
            handleDelete(selectedItem) {
                this.waiting = true;
                axios.delete("{{route('turmas.destroy', 'id')}}".replace('id', this.selectedItem.id))
                    .then(response =>{
                        this.waiting = false;
                        this.removed = true;
                        this.confirmDialog = false;
                        this.getTurmas();
                    })
                    .catch(error => {
                        this.error = true;
                        this.waiting = false;
                        this.confirmDialog = false;
                        this.snackTextTurma = "Ocorreu um erro na remoção"
                    });
            },
            handleTutor(item) {
                this.turma = item;
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

            //FORM TURMA METHODS
            getDisciplinas() {
                axios.get("{{route('disciplinas.index')}}")
                    .then(response => {
                        response.data.map((item) => {
                            this.disciplinas.push({
                                text: `${item.code} - ${item.name}`,
                                value: item.id,
                                carga: item.workload,
                            });
                        });
                    })
                    .catch(error => console.log(error));
            },
            getSemestres() {
                axios.get("{{route('semestres.index')}}")
                    .then(response => {
                        response.data.map((item) => {
                            this.semestres.push({
                                text: item.code,
                                value: item.id,
                            });
                        });
                    })
                    .catch(error => console.log(error));
            },
            successTextTurma(value) {
                value == 1
                    ? (this.snackTextTurma = "Turma Criada com Sucesso!")
                    : (this.snackTextTurma = "Turmas Criadas com Sucesso");
            },
            formTurmaHandleSubmit() {
                if (!this.correctWorkload && !this.correctTime) {
                    if (this.$refs.addTurma.validate()) {
                        this.waiting = true;
                        axios.post("{{route('disciplinas-ofertadas.store')}}", this.formTurma)
                            .then(response => {
                                var disciplina = response.data;
                                var i = 0;
                                while (i < disciplina.number_of_classes) {
                                    axios.post("{{route('turmas.store')}}", {
                                        disciplina_id: disciplina.id,
                                        code: `P0${i + 1}`,
                                        class_days: this.classes.days.toString(),
                                        class_time: this.classes.time.toString(),
                                    }).then(response=>{
                                            this.stored = true;
                                            this.successTextTurma(this.formTurma.number_of_classes);
                                        })
                                        .catch(error => {
                                            this.error = true;
                                            this.snackTextTurma = "Ocorreu um erro na criação"
                                        });
                                    i++;
                                }
                                this.waiting = false;
                                this.dialogTurma = false;
                                this.formTurma = {};
                                this.$refs.addTurma.reset();
                                this.getTurmas();
                            })
                        .catch(error => console.log(error));
                    }
                }
            },
            closeTurmaModal(){
                formTurma = {};
                this.$refs.addTurma.reset();
                this.dialogTurma = false;
            },
            closeTutorModal(){
                this.dialogTutor = false;
                this.cancelTutor();
            },
            handleError(error) {
                switch (error.field) {
                    case "code": {
                        this.errorMessages.code = error.message;
                        break;
                    }
                }
            },

            //FORM TUTOR METHODS
            async formTutorHandleSubmit() {
                try {
                    this.waiting = true;
                    for (const item of this.selectedTutores) {
                        var turma_tutor = { user_id: item.value, turma_id: this.turma.id };
                        await axios.post("{{route('turma-tutor.store')}}", turma_tutor);
                    }
                    this.dialogTutor = false;
                    this.successTextTutor(this.selectedTutores.length);
                    this.stored = true;
                    this.cancelTutor();
                    this.waiting = false;
                } catch (error) {
                    console.log(error);
                }
            },
            getTutores() {
                this.selectedTutores = [];
                this.tutores = [];
                this.alocados = [];
                axios.get("{{route('users.tipo', 'typeId')}}".replace('typeId', 2))
                    .then(response => {
                        response.data.map((item) => {
                            this.tutores.push({
                                value: item.id,
                                text: `${item.first_name} ${item.surname}`,
                            });
                        });
                    })
                    .catch(error => console.log(error))

                axios.get("{{route('turmas.tutor', 'turmaId')}}".replace('turmaId', this.turma.id))
                    .then(response => {
                        this.turma_tutor = response.data;
                        this.turma_tutor.map((item) => {
                            this.alocados.push({
                                value: item.user_id,
                                text: `${item.first_name} ${item.surname}`,
                            });
                        });
                    })
                    .catch(error => console.log(error));
                this.dialogTutor = true;
            },
            cancelTutor() {
                this.turma = null
                this.tutores = [];
                this.turma_tutor = [];
                this.selectedTutores = [];
                this.alocados = [];
            },
            successTextTutor(value) {
                value == 1
                    ? (this.snackTextTutor = "Tutor designado com sucesso!")
                    : (this.snackTextTutor = "Tutores designados com sucesso!");
            },
        }
    });
</script>
@endpush