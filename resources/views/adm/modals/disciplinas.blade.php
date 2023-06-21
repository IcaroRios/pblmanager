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
        @click:outside="closeModal()"
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
              @{{ update == true ? "Editar disciplina" : "Adicionar nova disciplina" }}
            </h5></v-toolbar
          >
          <v-card-text class="pt-6">
            <v-form v-model="validForm" ref="addDisciplina">
                <v-text-field
                v-model="form.code"
                @keyup="form.code = $event.target.value.toUpperCase()"
                @keyup.enter="handleSubmit"
                :rules="codeRules"
                label="Código da disciplina"
                required
                :disabled="update"
                :error="errorMessages.code != null"
                :error-messages="errorMessages.code"
              ></v-text-field>
              <v-text-field
                v-model="form.name"
                :rules="nameRules"
                label="Nome da disciplina"
                required
                @keyup.enter="update == true ? handleUpdate() : handleSubmit()"
              ></v-text-field>
              <v-select
                v-model="form.workload"
                :rules="workloadRules"
                label="Carga horária da disciplina"
                required
                @keyup.enter="update == true ? handleUpdate() : handleSubmit()"
                :items="workload"
              ></v-select>
              <v-select
                v-model="form.departamento_id"
                :rules="departamentoRules"
                label="Departamento"
                required
                @keyup.enter="update == true ? handleUpdate() : handleSubmit()"
                :items="departamentos"
                :disabled="update"
              ></v-select>
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn
              text
              color="red darken-1"
              @click="closeModal()"
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