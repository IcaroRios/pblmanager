@php
    
@endphp

@extends('layout.main')

@push("header")
<style type="text/css">
    .card-turma:hover {
      cursor: pointer;
    }

    .note-group-select-from-files {
        display: none;
    }
</style>

<!-- include libraries(jQuery, bootstrap) -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
@endpush

@section("content")
@include('layout.navbar')
<v-main id="turma">
    <v-container>
      <v-row id="turma-title" class="mt-2">
        <v-col offset-md="12">
          <v-card >
              <v-card-title>
                  <h3>Editar Problema</h3>
                  <v-spacer></v-spacer>
                  <v-btn 
                    color="primary"
                    style="background-color: var(--primary-dark-color)" 
                    href="{{ url()->previous() }}"
                  >
                    Voltar
                </v-btn>
              </v-card-title>
              <v-card-subtitle >
                <v-form v-model="validForm" ref="UpdateProblema">
                    <v-text-field
                    v-model="form.title"
                    @keyup.enter="handleSubmit"
                    :rules="title"
                    label="Nome do problema"
                    required
                    :error="errorMessages.name != null"
                    :error-messages="errorMessages.name"
                    ></v-text-field>
                    <v-menu
                    ref="menu1"
                    v-model="menu1"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="auto"
                    >
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                        v-model="dateFormatted"
                        label="Data Entrega"
                        persistent-hint
                        prepend-icon="mdi-calendar"
                        v-bind="attrs"
                        @blur="form.data_entrega = parseDate(dateFormatted)"
                        v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="form.data_entrega"
                        no-title
                        @input="menu1 = false"
                    ></v-date-picker>
                    </v-menu>
                    <v-textarea
                        v-model="form.description"
                        @keyup.enter="handleSubmit"
                        :rules="descriptionRules"
                        label="Descrição do problema"
                        required
                        :error="errorMessages.name != null"
                        :error-messages="errorMessages.name"
                    ></v-textarea>
                    <hr>
                    <h4><b>Conteúdo</b></h4>
                    <p>Insira o conteúdo como texto editável ou como um arquivo</p>
                    <div class="summernote"></div>
                    <v-file-input
                        show-size
                        truncate-length="17"
                        label="Anexo"
                        id="file"
                        ref="file"
                        v-on:change="onChangeFileUpload()"
                    ></v-file-input>
                    <div class="row" v-if="filePath">
                        <div class="col-md-12">
                            <p>Esse problema possui um arquivo, caso deseje alterá-lo, basta fazer o upload do novo arquivo</p>
                        </div>
                    </div>

                </v-form>
                <div class="text-right">
                    <v-btn style="background-color: var(--primary-dark-color)" color="primary" @click.prevent="handleUpdate()">
                        Editar
                    </v-btn>
                </div>
                <button v-if="filePath" class="btn btn-warning btn-sm" @click="downloadFile()">
                    Baixar Arquivo
                </button>
            </v-card-subtitle>
        </v-card>
        </v-col>
      </v-row>
      <v-snackbar v-model="updated" color="success" right bottom>
        <h6 style="margin: 0px !important">
            Problema editado com sucesso
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide('update')">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
    </v-container>
</v-main>
@endsection

@push("scripts")
<script>
        $(document).ready(function() {
        var ColunasButton = function (context) {
            var ui = $.summernote.ui;
            var button = ui.button({
                contents: '<i class="fa fa-child"/> Adicionar Colunas',
                tooltip: 'colunas',
                click: function () {
                context.invoke('editor.pasteHTML', '<div class="row">  <div class="column"> <p>coluna1</p> </div>  <div class="column"> <p>coluna2</p> </div></div><style>* {  box-sizing: border-box;}.row {  display: flex;}/* Create two equal columns that sits next to each other */.column {  flex: 50%;  padding: 10px;  height: 300px; /* Should be removed. Only for demonstration */}</style>');
                }
            });
            return button.render();
        }
    
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['ParagraphStyle', ['style']],
                ['fontname', ['fontname']],
                ['style', ['bold', 'italic', 'underline','strikethrough','clear']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height','hr']],
                ['tables', ['table']],
                ['insert',['link','picture','video']],
                ['view', ['fullscreen','codeview']],
                ['mybutton', ['colunas']]
            ],
            buttons: {
                colunas: ColunasButton
            }
        });
    });
    var turma = new Vue({
        el: '#turma',
        vuetify: new Vuetify(),
        data: {
            turma: {},
            problemas: [],
            waiting: false,
            removed: false,
            filePath: null,

            //Problema Data
            form: {
                title: "",
                description: "",
                body: "",
                data_entrega: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
                anexo: null
            },
            dateFormatted: "",
            menu1: false,
            dialog: false,
            validForm: undefined,
            title: [(v) => !!v || "Nome do problema é um campo obrigatório"],
            descriptionRules: [(v) => !!v || "descrição do problema é um campo obrigatório"],
            updated: false,
            errorMessages: { name: null },
        },

        created(){
            this.dateFormatted = this.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10));
            this.getProblema();
        },

        watch: {
            form:{
                handler(){
                    this.dateFormatted = this.formatDate(this.form.data_entrega)
                },
                deep: true
            },
        },

        computed: {
            snackText() {
                return "Problema Atualizado com Sucesso!";
                },
            computedDateFormatted () {
                return this.formatDate(this.date)
            },
        },

        methods: {
            objectToFormData(obj, form, namespace) {
                var fd = form || new FormData();
                var formKey;

                for(var property in obj) {
                    if(obj.hasOwnProperty(property)) {
                        if(namespace) formKey = namespace + '[' + property + ']';
                        else formKey = property;
                        if(typeof obj[property] === 'object' && !(obj[property] instanceof File)) {
                            this.objectToFormData(obj[property], fd, property);
                        } else {
                            fd.append(formKey, obj[property]);
                        }
                    }
                }
                return fd;
            },

            getProblema(){
                let splittedUrl = location.href.split('/');
                axios.get("{{route('problemas.show', 'id')}}".replace('id', splittedUrl[splittedUrl.length - 1]))
                    .then(response => {
                        console.log(response)
                        var ColunasButton = function (context) {
                            var ui = $.summernote.ui;
                            var button = ui.button({
                                contents: '<i class="fa fa-child"/> Adicionar Colunas',
                                tooltip: 'colunas',
                                click: function () {
                                context.invoke('editor.pasteHTML', '<div class="row">  <div class="column"> <p>coluna1</p> </div>  <div class="column"> <p>coluna2</p> </div></div><style>* {  box-sizing: border-box;}.row {  display: flex;}/* Create two equal columns that sits next to each other */.column {  flex: 50%;  padding: 10px;  height: 300px; /* Should be removed. Only for demonstration */}</style>');
                                }
                            });
                            return button.render();
                        }
                        $('.summernote').summernote({
                            height: 200,
                            toolbar: [
                                ['ParagraphStyle', ['style']],
                                ['fontname', ['fontname']],
                                ['style', ['bold', 'italic', 'underline','strikethrough','clear']],
                                ['fontsize', ['fontsize']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['height', ['height','hr']],
                                ['tables', ['table']],
                                ['insert',['link','picture','video']],
                                ['view', ['fullscreen','codeview']],
                                ['mybutton', ['colunas']]
                            ],
                            buttons: {
                                colunas: ColunasButton
                            }
                        });
                        $('.summernote').summernote('code', response.data.body);
                        this.form = {
                            title: response.data.title,
                            description: response.data.description,
                            data_entrega: response.data.data_entrega
                        }
                        this.filePath = response.data.file_path;
                    })
                    .catch(error => {
                        console.log(error);
                        error.response.data.message.forEach((item) => {
                            this.handleError(item);
                        });
                    })
            },

            downloadFile(){
                axios.post("{{route('file.download')}}", {path : this.filePath})
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

            //Problema Methods
            onChangeFileUpload(){
                this.form.anexo = document.getElementById('file').files[0];
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
            handleUpdate() {
                if (this.$refs.UpdateProblema.validate()) {
                    let splittedUrl = location.href.split('/');
                    this.form.title.trim();
                    this.form.description.trim();
                    this.form.body = $('.summernote').summernote('code');
                    this.form.disciplina_ofertada_id = this.turma.turma_id;
                    var data = this.objectToFormData(this.form);
                    axios.post("{{route('problemas.update', 'id')}}".replace('id', splittedUrl[splittedUrl.length - 1]), 
                        data, {
                            headers: {'Content-Type': `multipart/form-data;`}
                        }
                    )
                        .then(response => {
                            this.form = { title: "", description: "", data_entrega: null, anexo: null};
                            this.updated = true;
                            this.$refs.UpdateProblema.reset();
                            this.getProblema();
                        })
                        .catch(error => {
                            console.log(error);
                            error.response.data.message.forEach((item) => {
                                this.handleError(item);
                            });
                        });
                }
            },
            handleError(error) {
                switch (error.field) {
                    case "name": {
                        this.errorMessages.name = error.message;
                        break;
                    }
                }
            },
            hide() {
                this.updated = false;
            },
        },
    });
</script>
@endpush