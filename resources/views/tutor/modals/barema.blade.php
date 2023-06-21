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
          v-if="baremas?.length == 0"
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
              @{{ update == true ? "Editar barema" : "Adicionar novo barema" }}
            </h5></v-toolbar
          >
  
          <v-card-text class="pt-6">
            <v-form v-model="validForm" ref="addBarema">
              <v-text-field
                v-model="form.name"
                @keyup.enter="formHandleSubmit"
                :rules="nameRules"
                label="Nome do Barema"
                required
                :error="errorMessages.name != null"
                :error-messages="errorMessages.name"
              ></v-text-field>
              Critérios do Barema
              <v-card outlined>
                <v-form v-model="validForm" ref="itemBarema">
                  <v-row class="ml-1">
                    <v-col md="6" style="padding-bottom: 0px !important;">
                      <v-text-field
                        v-model="item.name"
                        label="Nome"
                        :error="!errorItem"
                        :error-messages="errorItem.name"
                      >
                      </v-text-field>
                    </v-col>
                    <v-col md="4" style="padding-bottom: 0px !important;">
                      <v-text-field
                        v-model="item.amount"
                        :max="maxValue"
                        label="Peso"
                        type="number"
                        :error="errorItem.amount != null"
                        :error-messages="errorItem.amount"
                        :hint="
                          errorItem.amount == null
                            ? `Peso disponível: ${maxValue}`
                            : ''
                        "
                        persistent-hint
                      >
                      </v-text-field>
                    </v-col>
                    <v-col
                      offset-md="1"
                      md="1"
                      class="d-flex justify-end align-center "
                      style="padding-bottom: 0px"
                    >
                      <v-btn icon color="primary" @click="addItem">
                        <v-icon>mdi-plus</v-icon>
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-form>
                <v-container>
                  <v-chip
                    v-for="item in form.itens"
                    :key="item.name"
                    class="ma-2"
                    color="var(--primary-light-color)"
                    label
                    text-color="white"
                  >
                    <v-icon left class="mr-1">
                      mdi-checkbox-marked-outline
                    </v-icon>
                    @{{ item.name }}
                    <v-icon right small class="mr-1">
                      @{{ item.amount }}
                    </v-icon>
                    <v-icon right small class="mr-1" @click="removeItem(item.id)">
                      mdi-close-circle-outline
                    </v-icon>
                  </v-chip>
                </v-container>
              </v-card>
            </v-form>
            <v-alert dense outlined type="error" v-show="noItens" class="mt-2"
              >Não é possível adicionar um barema sem critérios.
            </v-alert>
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