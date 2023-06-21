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
        v-model="matriculaDialog"
        max-width="70%"
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
              Matricular Alunos
            </h5>
          </v-toolbar>
          <v-card-text class="pt-6">
            <v-form v-model="validForm" ref="matriculaForm" class="overflow-y-auto overflow-x-hidden" style="max-height: 600px;">
              <v-row v-for="aluno in todosAlunos" :key="aluno.id" style="display: flex; flex-direction: row; justify-content: space-between; margin: auto">
                <label :for="aluno.id">@{{aluno.first_name}} @{{aluno.surname}} - @{{aluno.enrollment}}</label>
                <input type="hidden" name="alunos[]" v-model="matriculaForm.alunos[aluno.id]">
                <input class="form-control" type="checkbox" :id="aluno.id" v-model="matriculaForm.alunos[aluno.id]">
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
              @click.prevent="formMatriculaHandleSubmit()"
            >
                Matricular
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </template>