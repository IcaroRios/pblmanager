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
        v-model="presencaDialog"
        max-width="500"
        :disabled="waiting"
        :persistent="waiting"
        @click:outside="cancelForm"
      >
        <v-card>
          <v-toolbar color="var(--primary-dark-color)" style="color: white"
            ><v-progress-linear
              v-if="waiting == true"
              indeterminate
            ></v-progress-linear>
            <h5>
              Presença
            </h5>
          </v-toolbar>
          <v-btn text color="primary" @click="setAllTrue" style="width: 100%" class="mt-2">
            Todos presentes
            </v-btn>
          <v-card-text class="pt-6">
            <v-form v-model="validForm" ref="presencaForm">
                <v-row v-for="(aluno, index) in studentData" :key="aluno.id" style="display: flex; flex-direction: row; justify-content: space-between">
                    <label :for="aluno.id">@{{aluno.name}}</label>
                    <input type="hidden" name="alunos[]" v-model="presencaForm.alunos[index]">
                    <input class="form-control" type="checkbox" :id="aluno.id" name="presencas" v-model="presencaForm.presenca[index]">
                </v-row>
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn text color="red darken-1" @click="cancelForm()">
              Cancelar
            </v-btn>
            <v-btn
              text
              color="light-blue darken-4"
              @click.prevent="formPresencaHandleSubmit()"
            >
                Salvar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </template>