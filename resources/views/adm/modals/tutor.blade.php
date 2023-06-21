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
        <v-card>
          <v-toolbar color="var(--primary-dark-color)" style="color: white"
            ><v-progress-linear
              v-if="waiting == true"
              indeterminate
            ></v-progress-linear>
            <h5>
              Gerenciar Tutores
            </h5></v-toolbar
          >
          <v-card-text class="pt-6">
            <v-form ref="addTutor">
                <div v-if="alocados.length >= 1">
                    <p>Tutores previamente designados para a disciplina</p>
                    <v-chip
                      v-for="item in alocados"
                      :key="item.value"
                      class="ma-2"
                      color="blue"
                      text-color="white"
                      label
                    >
                      <v-avatar left>
                        <v-icon>mdi-account-circle</v-icon>
                      </v-avatar>
                      @{{ item.text }}
                    </v-chip>
                  </div>
      
                  <v-combobox
                    v-model="selectedTutores"
                    :items="selectableTutores"
                    label="Tutores disponíveis"
                    multiple
                    chips
                    no-data-text="Nenhum tutor disponível"
                    clearable
                    :disabled="selectableTutores.length == 0"
                    :persistent-hint="true"
                    :hint="
                      selectableTutores.length == 0
                        ? 'Nenhum tutor disponível para a disciplina'
                        : ''
                    "
                  ></v-combobox>
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn
              text
              color="red darken-1"
              @click="closeTutorModal()"
              >Cancelar</v-btn
            >
            <v-btn
              text
              color="light-blue darken-4"
              @click.prevent="formTutorHandleSubmit()"
              >Adicionar</v-btn
            >
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
    </div>
  </template>