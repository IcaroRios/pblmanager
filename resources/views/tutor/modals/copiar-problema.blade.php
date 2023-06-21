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
      v-model="copyDialog"
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
                    Copiar Problema
                </h5>
            </v-toolbar>

            <v-card-text class="pt-6">
                <v-form v-model="validForm" ref="CopyProblema">
                    <v-select
                        v-model="selectedTutorTurma"
                        item-text="text"
                        item-value="value"
                        label="Selecione a disciplina que deseja copiar o problema"
                        required
                        :items="tutorTurmas"
                    >
                    </v-select>
                </v-form>
            </v-card-text>
            <v-card-actions class="justify-end">
                <v-btn text color="red darken-1" @click="cancelForm()">
                    Cancelar
                </v-btn>
                <v-btn
                    text
                    color="light-blue darken-4"
                    @click.prevent="handleCopy()"
                >
                    Copiar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    </div>
</template>