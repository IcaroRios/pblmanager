@php

@endphp

@extends('layout.main')

@push("header")

@endpush

@section("content")
@include('layout.navbar')
<v-app id="barema">
    <v-main>
        <v-container>
            <v-row id="meus-baremas" class="mt-2">
              <v-col offset-md="2" md="8" sm="12">
                <v-card>
                  <v-progress-linear
                    indeterminate
                    v-if="baremas == undefined"
                  ></v-progress-linear>
                  <v-card-title>
                    <v-row no-gutter align="center">
                      <v-col class="d-flex" cols="12" sm="12">
                        <h4>Barema do problema: @{{problema.title}}</h4>
                      </v-col>
                      <v-btn
                          class="ms-3 btn-sm"
                          color="primary"
                          style="background-color: var(--primary-dark-color)"
                          href="{{url()->previous()}}"
                        >
                            Voltar
                      </v-btn>
                      <v-col class="d-flex" cols="12" sm="3">
                        @component('tutor.modals.barema') @endcomponent
                      </v-col>
                    </v-row>
                  </v-card-title>

                  <v-card-subtitle class="mt-2">
                    <v-expansion-panels accordion>
                      <v-expansion-panel
                        v-for="barema in baremas"
                        :key="barema.id"
                      >
                        <v-expansion-panel-header>
                          <h6>@{{ barema.name }}</h6>
                        </v-expansion-panel-header>

                        <v-expansion-panel-content>
                          <v-tooltip top v-for="item in barema.item_baremas" :key="item.id">
                            <template v-slot:activator="{ on, attrs }">
                              <v-chip
                                v-bind="attrs"
                                v-on="on"
                                class="ma-2"
                                color="var(--primary-light-color)"
                                label
                                text-color="white"
                              >
                                <v-icon left>
                                  mdi-checkbox-marked-outline
                                </v-icon>
                                @{{ item.name }}
                              </v-chip>
                            </template>
                            <span>Peso: @{{ item.amount }}</span>
                          </v-tooltip>
                          <v-row class="mt-1 mb-1 d-flex justify-end">
                            <v-btn
                              @click="setDataToUpdate(barema)"
                              elevation="2"
                              small
                              color="warning"
                              tile
                            >
                              <v-icon left> mdi-pencil </v-icon>Editar</v-btn
                            >
                            <v-btn
                              @click="
                                () => {
                                  selectedItem = barema.id;
                                  confirmDialog = true;
                                }
                              "
                              elevation="2"
                              small
                              color="error"
                              tile
                              class="ml-2"
                              ><v-icon left> mdi-delete </v-icon>Remover</v-btn
                            >
                          </v-row>
                        </v-expansion-panel-content>
                      </v-expansion-panel>
                    </v-expansion-panels>
                  </v-card-subtitle>
                </v-card>
              </v-col>
            </v-row>
            <v-dialog
              v-model="confirmDialog"
              max-width="500"
              :disabled="waiting"
              :persistent="waiting"
              @keydown.enter="handleDelete"
            >
              <v-card>
                <v-toolbar color="var(--primary-dark-color)" style="color: white"
                  ><h6>Deseja remover o item selecionado?</h6></v-toolbar
                >
                <v-progress-linear
                  v-if="waiting == true"
                  indeterminate
                ></v-progress-linear>
                <v-card-actions class="justify-end">
                  <v-btn
                    text
                    color="red darken-1"
                    @click="confirmDialog = false"
                    :disabled="waiting"
                    >Cancelar</v-btn
                  >
                  <v-btn
                    text
                    color="light-blue darken-4"
                    @click="handleDelete"
                    :disabled="waiting"
                    >Remover</v-btn
                  >
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-snackbar v-model="removed" color="success" right bottom>
                <h6 style="margin: 0px !important">
                    Barema removido com sucesso!
                </h6>
                <template v-slot:action="{ attrs }">
                    <v-btn color="white" text v-bind="attrs" @click="hide('remove')">
                        Fechar
                    </v-btn>
                </template>
            </v-snackbar>
          </v-container>
    </v-app>
</v-main>
@endsection

@push("scripts")
<script>
    var barema = new Vue({
        el: '#barema',
        vuetify: new Vuetify(),
        data: {
            problema: {
              title: ''
            },
            baremas: undefined,
            selectedItem: undefined,
            confirmDialog: false,
            waiting: false,
            removed: false,
            dataToUpdate: undefined,

            //Barema Modal
            form: { name: "", itens: [] },
            item: { name: "", amount: "" },
            dialog: false,
            validForm: undefined,
            nameRules: [(v) => !!v || "Nome do Barema é um campo obrigatório"],
            itensRules: [
                (v) => v.length >= 1 || "O Barema deve conter ao menos 1 critério",
            ],
            stored: false,
            update: false,
            updated: false,
            errorMessages: { name: null },
            errorItem: { name: null, amount: null },
            noItens: false,
        },

        computed: {
            snackText() {
                return this.updated == false
                    ? "Barema Adicionado com Sucesso!"
                    : "Barema Atualizado com Sucesso!";
            },
            maxValue() {
                let total = 0;
                this.form.itens.forEach((item) => {
                    total += parseInt(item.amount);
                });
                return 100 - total;
            }
        },

        created(){
          this.getProblema();
          this.getBaremas();
        },

        methods: {
            getProblema(){
              let splittedUrl = location.href.split('/');
              axios.get("{{route('problemas.show', 'id')}}".replace('id', splittedUrl[splittedUrl.length - 1]))
                  .then(response => {
                    this.problema = response.data;
                  })
                  .catch(error => console.log(error))
            },
            getBaremas(){
              let splittedUrl = location.href.split('/');
              axios.get(`{{route('baremas.index')}}?problema=${splittedUrl[splittedUrl.length - 1]}`)
                  .then(response => {
                    this.baremas = response.data;
                  })
                  .catch(error => console.log(error))
            },
            handleDelete() {
              this.waiting = true;
              axios.delete("{{route('baremas.destroy', 'id')}}".replace('id', this.selectedItem))
                .then(response => {
                  this.waiting = false;
                  this.removed = true;
                  this.confirmDialog = false;
                  this.getBaremas();
                })
                .catch(error => {
                  console.log(error);
                });
            },
            //Barema Modal
            formHandleSubmit() {
              this.waiting = true;
              let splittedUrl = location.href.split('/');
              this.form.name.trim();
              this.form.problema_id = splittedUrl[splittedUrl.length - 1];
              if (this.form.itens.length == 0) {
                this.noItens = true;
              } else {
                if (this.$refs.addBarema.validate()) {
                  this.noItens = false;
                  axios.post("{{route('baremas.store')}}", this.form)
                    .then(response => {
                      this.waiting = false;
                      this.dialog = false;
                      this.stored = true;
                      this.form = { name: "", itens: [] };
                      this.item = { name: "", amount: "" };
                      this.$refs.addBarema.reset();
                      this.errorMessages.name = null;
                      this.getBaremas();
                    })
                    .catch(error => {
                      console.log(error.response);
                      error.response.data.message.forEach(item => {
                        this.handleError(item);
                      })
                    });
                }
              }
            },
            setDataToUpdate(item) {
              this.dataToUpdate = item.id;
              this.form.name = item.name;
              this.form.itens = item.item_baremas;
              this.dialog = true;
              this.update = true;
            },
            formHandleUpdate() {
              this.waiting = true;
              if (this.form.itens.length == 0) {
                this.noItens = true;
              } else {
                if (this.$refs.addBarema.validate()) {
                  this.noItens = false;
                  axios.put("{{route('baremas.update', 'id')}}".replace('id', this.dataToUpdate), this.form)
                    .then(response => {
                      this.waiting = false;
                      this.dialog = false;
                      this.updated = true;
                      this.update = false;
                      this.dataToUpdate = null;
                      this.form = { name: "", itens: [] };
                      this.item = { name: "", amount: "" };
                      this.$refs.addBarema.reset();
                      this.getBaremas();
                    })
                    .catch(error => {
                      console.log(error.response);
                      error.response.data.message.forEach((item) => {
                        this.handleError(item);
                      });
                    });
                }
              }
            },
            cancelUpdate() {
              this.update = false;
              this.dataToUpdate = null;
            },
            cancelForm() {
              this.dialog = false;
              this.errorItem = { name: null, amount: null };
              this.$refs.addBarema.reset();
              this.form = { name: "", itens: [] };
              this.item = { name: "", amount: "" };
              if (this.update == true) this.cancelUpdate();
              this.errorMessages.name = null;
            },
            handleError(error) {
              switch (error.field) {
                case "name": {
                  this.errorMessages.name = error.message;
                  break;
                }
              }
            },
            addItem() {
              if (this.item.name != "" && this.item.amount != "") {
                  if (this.item.amount > 100) {
                  this.errorItem.amount = "O peso máximo é 100";
                  } else if (this.item.amount == 0) {
                  this.errorItem.amount = "O peso não pode ser 0";
                  } else {
                  let total = 0;
                  this.form.itens.forEach((item) => {
                      console.log(item);
                      total += parseInt(item.amount);
                  });
                  if (total >= 100) {
                      this.errorItem.amount =
                      "O total de pesos dos critérios desse ser no máximo 100";
                  } else {
                      this.form.itens.push({
                      name:
                          this.item.name.charAt(0).toUpperCase() +
                          this.item.name.slice(1),
                      amount: this.item.amount,
                      });
                      this.item.name = "";
                      this.item.amount = "";
                  }
                  }
              } else if (this.item.name == "" && this.item.amount != "") {
                  this.errorItem.name = "Nome do Critério é um campo obrigatório";
              } else if (this.item.name != "" && this.item.amount == "") {
                  this.errorItem.amount = "Peso do Critério é um campo obrigatório";
              } else {
                  this.errorItem.name = "Nome do Critério é um campo obrigatório";
                  this.errorItem.amount = "Peso do Critério é um campo obrigatório";
              }
            },
            removeItem(id) {
              this.form.itens = this.form.itens.filter((criterio) => {
                  return criterio.id != id;
              });
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
        },
    });
</script>
@endpush