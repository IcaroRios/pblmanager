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
        v-model="dialogTurma"
        max-width="500"
        :disabled="waiting"
        :persistent="waiting"
        @click:outside="closeTurmaModal()"
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
              Adicionar Nova Turma
            </h5></v-toolbar
          >
          <v-card-text class="pt-6">
            <v-form v-model="validTurmaForm" ref="addTurma">
                <v-select
                v-model="formTurma.disciplina_id"
                :rules="disciplinaRules"
                label="Disciplina"
                required
                @keyup.enter="formTurmaHandleSubmit"
                :items="disciplinas"
                ></v-select>
                <v-select
                v-model="formTurma.semestre_id"
                :rules="semestreRules"
                label="Semestre"
                required
                @keyup.enter="formTurmaHandleSubmit"
                :items="semestres"
                ></v-select>

                <v-text-field
                type="number"
                v-model="formTurma.number_of_classes"
                @keyup.enter="formTurmaHandleSubmit"
                :rules="numberRules"
                label="Número de turmas"
                required
                ></v-text-field>
                <p class="mt-1 mb-1">
                Dias de Aula
                <small v-show="classes.days.length > 0">@{{this.classes.days}}</small>
                </p>
                <v-card outlined style="color: #ff5252 !important">
                <v-row class="m-0" no-gutter>
                    <v-col
                    md="4"
                    v-for="day in days"
                    :key="day.value"
                    class="pt-0 pb-0"
                    >
                    <v-checkbox
                        dense
                        v-model="classes.days"
                        :label="day.text"
                        :value="day.value"
                        :disabled="formTurma.disciplina_id == null"
                    ></v-checkbox>
                    </v-col>
                </v-row>
                </v-card>
                <div v-show="correctWorkload">
                <small style="color: #ff5252 !important"
                    >O número de dias não condiz com a carga horária da
                    disciplina</small
                >
                </div>
                <p class="mt-4 mb-1">
                Horário das Aulas
                <small v-show="classes.time.length > 0">@{{this.classes.time}}</small>
                </p>
                <v-card outlined>
                <v-row class="m-0" no-gutter>
                    <v-col
                    md="4"
                    v-for="t in times"
                    :key="t.value"
                    class="pt-0 pb-0"
                    >
                    <v-checkbox
                        dense
                        v-model="classes.time"
                        :label="t.text"
                        :value="t.value"
                        :disabled="formTurma.disciplina_id == null"
                    ></v-checkbox>
                    </v-col>
                </v-row>
                </v-card>
                <div v-show="correctTime">
                <small style="color: #ff5252 !important"
                    >A quantidade de horários não condiz com a carga horária da
                    disciplina</small
                >
                </div>
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn
              text
              color="red darken-1"
              @click="closeTurmaModal()"
              >Cancelar</v-btn
            >
            <v-btn
              text
              color="light-blue darken-4"
              @click.prevent="formTurmaHandleSubmit()"
              >Adicionar</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="stored" color="success" right bottom>
        <h6 style="margin: 0px !important">
            @{{snackTextTurma}}
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide('store')">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
      <v-snackbar v-model="error" color="error" right bottom>
        <h6 style="margin: 0px !important">
          @{{snackTextTurma}}
        </h6>
        <template v-slot:action="{ attrs }">
            <v-btn color="white" text v-bind="attrs" @click="hide('error')">
                Fechar
            </v-btn>
        </template>
      </v-snackbar>
    </div>
  </template>