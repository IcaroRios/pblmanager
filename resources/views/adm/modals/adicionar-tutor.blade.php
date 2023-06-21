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
        v-model="dialogTutor"
        max-width="500"
        :disabled="waiting"
        :persistent="waiting"
        @click:outside="closeTutorModal()"
      >
        <v-btn
          slot="activator"
          slot-scope="props"
          v-on="props.on"
          color="var(--primary-color)"
          style="color: white"
        >
          <v-icon left> mdi-plus </v-icon> Adicionar
        </v-btn>
        <v-card>
          <v-toolbar color="var(--primary-dark-color)" style="color: white"
            ><v-progress-linear
              v-if="waiting == true"
              indeterminate
            ></v-progress-linear>
            <h5>
              Adicionar Novo Tutor
            </h5></v-toolbar
          >
          <v-card-text class="pt-6">
            <v-form v-model="validTutorForm" ref="addTutor">
              <v-text-field
                v-model="formTutor.fullName"
                :rules="nameRules"
                label="Nome completo"
                prepend-icon="mdi-account"
                type="text"
              ></v-text-field>
              <v-text-field
                v-model="formTutor.username"
                :rules="usernameRules"
                label="Nome de usuário"
                prepend-icon="mdi-account"
                type="text"
              ></v-text-field>
              <v-text-field
                v-model="formTutor.email"
                :rules="emailRules"
                label="Email"
                prepend-icon="mdi-email"
                type="email"
              ></v-text-field>
              <v-text-field
                :rules="passwordRules"
                v-model="formTutor.password"
                label="Senha"
                prepend-icon="mdi-lock"
                type="password"
              ></v-text-field>
              <v-text-field
                :rules="passwordRules"
                v-model="formTutor.password_confirmation"
                label="Repetir Senha"
                prepend-icon="mdi-lock"
                type="password"
              ></v-text-field>
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn text color="red darken-1" @click="closeTutorModal()" >
              Cancelar
            </v-btn>
            <v-btn text color="light-blue darken-4" @click.prevent="formTutorHandleSubmit()" >
              Cadastrar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="stored" color="success" right bottom>
        <h6 style="margin: 0px !important">
            @{{snackTextTutor}}
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide('store')">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
      <v-snackbar v-model="error" color="error" right bottom>
        <h6 style="margin: 0px !important">
          @{{snackTextTutor}}
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide('error')">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
    </div>
  </template>