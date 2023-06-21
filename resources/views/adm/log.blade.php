@php
    
@endphp

@extends('layout.main')

@push("header")

@endpush

@section("content")
@include('layout.navbar')
<v-app id="log">
    <v-main>
        <v-container>
            <v-row id="disciplina-table" class="mt-2">
                <v-col offset-md="2" md="8" sm="12">
                  <v-card>
                    <v-card-title>
                      <h4>Logs do Sistema</h4>
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
                        <v-col class="d-flex" cols="12" sm="4">
                            <v-menu
                              v-model="menu"
                              :close-on-content-click="false"
                              :nudge-right="40"
                              transition="scale-transition"
                              offset-y
                              min-width="auto"
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                  v-model="dateQuery"
                                  label="Período"
                                  prepend-icon="mdi-calendar"
                                  readonly
                                  v-bind="attrs"
                                  v-on="on"
                                  clearable
                                ></v-text-field>
                              </template>
                              <v-date-picker
                                v-model="dateQuery"
                                multiple
                                locale="pt-br"
                                no-title
                                scrollable
                                :min="dateQuery ? dateQuery[0] : ''"
                                :disabled="dateQuery ? dateQuery.length == 2 : false"
                              ></v-date-picker>
                            </v-menu>
                          </v-col>
                          <v-col class="d-flex justify-end" cols="12" sm="3">
                            <v-btn
                              @click="generatePDF"
                              color="var(--primary-color)"
                              style="color: white"
                            >
                              <v-icon left> mdi-file-pdf-outline </v-icon> Gerar PDF
                            </v-btn>
                            </v-col>
                    </v-row>
                  </v-card-subtitle>
                  <!-- New -->
                  @component('layout.table')
                  @endcomponent
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </template>
    </v-app>
</v-main>
@endsection

@push("scripts")
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script>
    window.jsPDF = window.jspdf.jsPDF;
    var log = new Vue({
        el: '#log',
        vuetify: new Vuetify(),
        data: {
            data: undefined,
            searchQuery: "",
            headers: [
                { text: "Log", value: "log" },
                { text: "Data e Hora", value: "created_at", width: "30%" },
            ],
            dateQuery: [],
            menu: false,
            waiting: false,
            confirmDialog: false,
            handleDelete: null,
        },
        mounted(){
            this.componentStructure();
        },
        computed: {
            filteredList() {
                let search = this.removeSpecial(this.searchQuery.toLowerCase().trim());
                let itens = this.data;
                if (this.dateQuery) {
                    if (this.dateQuery.length == 2) {
                    itens = this.data.filter((item) => {
                        return this.compareDate(
                        item.created_at,
                        this.dateQuery[0],
                        this.dateQuery[1]
                        );
                    });
                    }
                }
                if (search != "") {
                    return itens.filter((item) => {
                    return (
                        this.removeSpecial(item.log.toLowerCase()).includes(search) ||
                        item.created_at.includes(search)
                    );
                    });
                } else {
                    return itens;
                }
            },
            notFound() {
                return "Ainda não ocorreram mudanças no sistema";
            },
        },
        watch:{
            dataToUpdate() {
                if (this.dataToUpdate != null) this.getOne();
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
            async componentStructure() {
                axios.get("{{route('logs.index')}}")
                    .then((response) => {
                        let items = response.data.reverse();
                        this.data = [];
                        items.map((item) => {
                            this.data.push({
                                log: item.log,
                                created_at: this.formatDateTime(item.created_at),
                            });
                        });
                    })
                    .catch(error => console.log(error));
            },
            formatDateTime(date) {
                var dateTime = date.split("T");
                var data = dateTime[0].split("-");

                var time = dateTime[1].split(".");
                time = time[0].split(":");
                time[0] = parseInt(time[0]) - 3;

                if (time[0] == -1) {
                    time[0] = 23;
                } else if (time[0] == -2) {
                    time[0] = 22;
                } else if (time[0] == -3) {
                    time[0] = 21;
                }
                if (time[0] < 10) {
                    time[0] = `0${time[0]}`;
                }
                time = `${time[0]}:${time[1]}:${time[2]}`;
                data = `${data[2]}-${data[1]}-${data[0]}`;
                return `${data} T${time}`;
            },
            compareDate(date, query1, query2) {
                var menor = query1.split("-");
                menor = `${menor[2]}-${menor[1]}-${menor[0]}`;

                var maior = query2.split("-");
                maior = `${maior[2]}-${maior[1]}-${maior[0]}`;

                var data = date.split("T")[0];
                data = data.trim();
                console.log(`${data}`);
                return data >= menor && data <= maior;
            },
            removeSpecial(texto) {
                texto = texto.replace(/[ÀÁÂÃÄÅ]/, "A");
                texto = texto.replace(/[àáâãäå]/, "a");
                texto = texto.replace(/[ÈÉÊË]/, "E");
                texto = texto.replace(/[Ç]/, "C");
                texto = texto.replace(/[ç]/, "c");
                return texto;
            },
            generatePDF() {
                const doc = new jsPDF();
                var items = [];
                this.filteredList.map((item) => {
                    items.push(item);
                });
                doc.autoTable({
                    body: items.reverse(),
                    columns: [
                    { header: "Log", dataKey: "log" },
                    { header: "Data e Hora", dataKey: "created_at" },
                    ],
                });

                doc.save("systemLog.pdf");
            },
        }
    });
</script>
@endpush