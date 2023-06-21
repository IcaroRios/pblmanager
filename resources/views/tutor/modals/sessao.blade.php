@push("header")
<style>
    .v-dialog__container{
        display:block !important;
    }
</style>
@endpush

<template>
    <div>
      <v-dialog
        v-model="dialog"
        max-width="500"
        :disabled="waiting"
        :persistent="waiting"
        @click:outside="cancelForm"
      >
        <v-btn
          slot="activator"
          slot-scope="props"
          v-on="props.on"
          color="var(--primary-color)"
          style="color: white"
        >
          <v-icon left> mdi-plus </v-icon> Adicionar Sessão
        </v-btn>
        <v-card>
          <v-toolbar color="var(--primary-dark-color)" style="color: white"
            ><v-progress-linear
              v-if="waiting == true"
              indeterminate
            ></v-progress-linear>
            <h5>
              @{{ update == true ? "Editar sessao" : "Adicionar nova sessao" }}
            </h5></v-toolbar
          >

          <v-card-text class="pt-6">
            <v-form v-model="validForm" ref="addSessao">
                <v-text-field
                    v-model="form.title"
                    @keyup.enter="formHandleSubmit"
                    :rules="nameRules"
                    label="Nome da Sessao"
                    required
                    :error="errorMessages.name != null"
                    :error-messages="errorMessages.name"
                ></v-text-field>
                <v-select
                  label="Problema"
                  :items="problemas"
                  v-model="form.problema_unidade_id"
                  item-text="title"
                  item-value="problema_id"
                  :rules="problemRules"
                ></v-select>
                <v-menu ref="menu1" v-model="menu1" :close-on-content-click="false" transition="scale-transition" offset-y max-width="290px" min-width="auto">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                      v-model="dateFormatted"
                      label="Data da Sessao"
                      persistent-hint
                      prepend-icon="mdi-calendar"
                      v-bind="attrs"
                      @blur="form.session_date = parseDate(dateFormatted)"
                      v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="form.session_date"
                        no-title
                        @input="menu1 = false"
                    ></v-date-picker>
                </v-menu>
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn text color="red darken-1" @click="cancelForm()"
              >Cancelar</v-btn
            >
            <v-btn
              text
              color="light-blue darken-4"
              @click.prevent="update == true ? formHandleUpdate() : formHandleSubmit()"
              >@{{ update == true ? "Confirmar edição" : "Adicionar" }}</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="stored" color="success" right bottom>
        <h6 style="margin: 0px !important">
            @{{snackText}}
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide('store')">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
      <v-snackbar v-model="updated" color="success" right bottom>
        <h6 style="margin: 0px !important">
            @{{snackText}}
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide('update')">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
    </div>
  </template>