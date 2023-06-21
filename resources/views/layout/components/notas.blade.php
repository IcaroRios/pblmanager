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
        v-model="notasDialog"
        max-width="500"
        @click:outside="() => {notasDialog = false}"
      >
        <v-card>
          <v-toolbar color="var(--primary-dark-color)" style="color: white">
            <h5>
              Notas
            </h5></v-toolbar
          >
          <v-card-text class="pt-6" v-for="problemaNota in problemaNotas" :key="problemaNota.id">
            <div style="display: flex; flex-direction: row; justify-content: space-between">
              <h4>@{{problemaNota.problema}}</h4>
              <h4><small>@{{problemaNota.media.toFixed(2)}}</small></h4>
            </div>
            <div v-for="(barema in problemaNota.notas" style="display: flex; flex-direction: row; justify-content: space-between">
                <p>@{{barema.avaliacao}} - Peso: @{{barema.peso}} </p>
                <p>@{{barema.nota.toFixed(2)}}</p>
            </div>
            <hr>
          </v-card-text>
          <v-card-text>
            <h4>Media Geral</h4>
            <h5 class="mt-3">@{{mediaGeral.toFixed(2)}}</h5>
          </v-card-text>
          <hr>
        </v-card>
      </v-dialog>
    </div>
  </template>