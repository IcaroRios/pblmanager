@push("header")
<style type="text/css">
  .function-title {
    margin-left: 8px !important;
  }
</style>
@endpush

<div id="navbar">
  <!-- Navbar -->
  <v-navigation-drawer v-model="drawer" app>
    <v-list-item>
        <v-list-item-content>
          <v-list-item-title class="title">
            <v-avatar color="grey" size="56">
            <v-icon x-large color="white">mdi-account</v-icon>
            </v-avatar>
          </v-list-item-title>
          <v-list-item-subtitle>
            <h6>@{{username}}</h6>
            <small>@{{type}}</small>
          </v-list-item-subtitle>
        </v-list-item-content>
    </v-list-item>
  <v-divider></v-divider>
  <!-- Function List -->
    <v-list nav>
      <v-list-item-group
        v-model="group"
        active-class="text--accent-4"
        color="var(--primary-color)"
      >
        <v-list-item
          shapped
          v-for="(item, idx) in navItems"
          :key="idx"
          @click="handleClick(item.value, item.redirect)"
        >
          <v-icon>@{{ item.icon }}</v-icon>
          <v-list-item-title style="margin-left: 8px">
          <p>@{{ item.title }}</p>
          </v-list-item-title>
        </v-list-item>
      </v-list-item-group>
    </v-list>
    <v-list-item shapped justify-end @click="logout">
      <v-icon>mdi-logout</v-icon>
      <v-list-item-title style="margin-left: 8px">
      Sair
      </v-list-item-title>
    </v-list-item>
  </v-navigation-drawer>

  <v-app-bar color="var(--primary-dark-color)" dark style="color: white">
    <v-app-bar-nav-icon
        @click="toggleDrawer"
    ></v-app-bar-nav-icon>
    <v-toolbar-title><h5>Ambiente Web</h5></v-toolbar-title>
    <v-spacer></v-spacer>
  </v-app-bar>
</div>

@push("scripts")
<script>
  var navbar = new Vue({
    el: "#navbar",
    vuetify: new Vuetify(),
    data:{
      drawer: false,
      group: 2,
      deviceWidth: null,
      username: "",
      type: "",
      navItems: [],
      navList: {
        "adm": [
          {
            "title": "Menu Inicial",
            "icon": "mdi-view-dashboard",
            "value": 0,
            "redirect": "{{route('adm.menu')}}"
          },
          {
            "title": "Departamentos",
            "icon": "mdi-office-building-outline",
            "value": 1,
            "redirect": "{{route('adm.departamentos')}}"
          },
          {
            "title": "Disciplinas",
            "icon": "mdi-notebook-multiple",
            "value": 2,
            "redirect": "{{route('adm.disciplinas')}}"
          },
          {
            "title": "Semestres",
            "icon": "mdi-calendar-multiple",
            "value": 3,
            "redirect": "{{route('adm.semestres')}}"
          },
          {
            "title": "Turmas",
            "icon": "mdi-google-classroom",
            "value": 4,
            "redirect": "{{route('adm.turmas')}}"
          },
          {
            "title": "Tutores",
            "icon": "mdi-account-supervisor",
            "value": 5,
            "redirect": "{{route('adm.tutores')}}"
          },
          {
            "title": "Log do Sistema",
            "icon": "mdi-format-list-bulleted",
            "value": 6,
            "redirect": "{{route('adm.log')}}"
          }
        ],
        "tutor": [
          {
            "title": "Turmas",
            "icon": "mdi-google-classroom",
            "value": 0,
            "redirect": "{{route('tutor.turmas')}}"
          },
          {
            "title": "Minha Agenda",
            "icon": "mdi-calendar",
            "value": 1,
            "redirect": "{{route('tutor.agenda')}}"
          },
          {
            "title": "Repositório de Problemas",
            "icon": "mdi-file-multiple",
            "value": 2,
            "redirect": "{{route('tutor.pesquisar-problema')}}"
          }
        ],
        "aluno": [
          {
            "title": "Turmas",
            "icon": "mdi-google-classroom",
            "value": 0,
            "redirect": "{{route('aluno.inicio')}}"
          },
        ]
      }
    },
    mounted(){
      this.username = sessionStorage.getItem("username");
      this.type = sessionStorage.getItem("type");
      this.loadMenu();
      this.group = parseInt(sessionStorage.getItem("group")) ?? 0;
    },
    methods: {
      loadMenu() {
        let user_type = sessionStorage.getItem("user_type");
        if (user_type == 1) this.navItems = this.navList.adm;
        else if (user_type == 2) this.navItems = this.navList.tutor;
        else if (user_type == 4) this.navItems = this.navList.aluno;
      },
      toggleDrawer(){
        this.drawer = !this.drawer;
      },
      handleClick(value, redirect) {
        if (this.deviceWidth <= 1264) {
          this.toggleDrawer();
        }

        sessionStorage.setItem("group", value);
        window.location = redirect;
      },
      logout() {
        axios.post("{{route('auth.logout')}}")
          .then(response => {
            sessionStorage.removeItem("token");
            sessionStorage.removeItem("user_type");
            sessionStorage.removeItem("username");
            window.location = response.data.redirect;
          })
          .catch(error => console.log(error));
      },
    },
    watch: {
      drawer() {
        this.deviceWidth =
          window.innerWidth > 0 ? window.innerWidth : screen.width;
      },
    },
});
</script>
@endpush