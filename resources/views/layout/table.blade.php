<div>
    <v-data-table
      :loading="data == undefined"
      :items="filteredList"
      :headers="headers"
      loading-text="Carregando... Por favor, aguarde"
      :items-per-page="10"
      class="elevation-1 row-pointer table"
      :no-data-text="notFound"
    >
      <template v-slot:item.action="{ item }">
        <v-btn
          @click="handleUpdate(item)"
          elevation="2"
          small
          color="warning"
          tile
        >
          <v-icon left> mdi-pencil </v-icon>Editar</v-btn
        >
        <v-btn
          @click="
            () => {
              confirmDialog = true;
              selectedItem = item;
            }
          "
          elevation="2"
          small
          color="error"
          tile
          class="ml-2"
          ><v-icon left> mdi-delete </v-icon>Remover</v-btn
        >
      </template>
      <template v-slot:item.action2="{ item }">
        <v-btn @click="handleTutor(item)" elevation="2" small color="info" tile
          ><v-icon left> mdi-account-supervisor </v-icon>Tutores</v-btn
        >
        <v-btn
          @click="
            () => {
              confirmDialog = true;
              selectedItem = item;
            }
          "
          elevation="2"
          small
          color="error"
          tile
          class="ml-2"
          ><v-icon left> mdi-delete </v-icon>Remover</v-btn
        >
      </template>
    </v-data-table>
    <v-dialog
      v-model="confirmDialog"
      max-width="500"
      :disabled="waiting"
      :persistent="waiting"
      @keydown.enter="handleDelete"
    >
      <v-card>
        <v-toolbar color="var(--primary-dark-color)" style="color: white"
          ><h6>Deseja remover o item selecionado?</h6></v-toolbar
        >
        <v-progress-linear
          v-if="waiting == true"
          indeterminate
        ></v-progress-linear>
        <v-card-actions class="justify-end">
          <v-btn
            text
            color="red darken-1"
            @click="confirmDialog = false"
            :disabled="waiting"
            >Cancelar</v-btn
          >
          <v-btn
            text
            color="light-blue darken-4"
            @click="handleDelete"
            :disabled="waiting"
            >Remover</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>