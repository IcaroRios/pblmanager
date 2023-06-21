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
              @{{ update == true ? "Editar semestre" : "Adicionar novo semestre" }}
            </h5></v-toolbar
          >
          <v-card-text class="pt-6">
            <v-form v-model="validForm" ref="addSemestre">
                <v-text-field
                    v-model="form.code"
                    @keyup="form.code = $event.target.value.toUpperCase()"
                    @keyup.enter="handleSubmit"
                    :rules="codeRules"
                    label="Código do semestre"
                    required
                    :disabled="update"
                    :error="errorMessages.code != null"
                    :error-messages="errorMessages.code"
                ></v-text-field>
                <v-menu
                    v-model="menu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="auto"
                    class="ms-5"
                >
                    <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="formattedStartDate"
                        label="Data de início do semestre"
                        prepend-icon="mdi-calendar"
                        v-bind="attrs"
                        v-on="on"
                        :rules="dateRules"
                        @blur="form.start_date = parseDate(formattedStartDate)"
                        @keyup.enter="handleSubmit"
                        :disabled="update"
                    ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.start_date"
                      @input="
                          () => {
                            menu = false;
                            form.end_date = null;
                          }
                      "
                      locale="pt-br"
                      no-title
                      scrollable
                    ></v-date-picker>
                </v-menu>
                <v-menu
                    v-model="menu2"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="auto"
                    class="ms-5"
                >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                          v-model="formattedEndDate"
                          label="Data de término do semestre"
                          prepend-icon="mdi-calendar"
                          v-bind="attrs"
                          v-on="on"
                          :rules="dateRules"
                          @blur="form.end_date = parseDate(formattedEndDate)"
                          @keyup.enter="update == true ? handleUpdate() : handleSubmit()"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      locale="pt-br"
                      no-title
                      scrollable
                      v-model="form.end_date"
                      @input="menu2 = false;"
                      :min="minEndDate"
                      :disabled="form.start_date == null || form.start_date == ''"
                    ></v-date-picker>
                </v-menu>
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