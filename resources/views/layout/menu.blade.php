@push("header")
<style>
  .wrimagecard {
    max-width: 344px;
    margin-top: 0;
    margin-bottom: 1.5rem;
    text-align: center;
    position: relative;
    background: #fff;
    box-shadow: 8px 10px 12px 0px rgba(46, 61, 73, 0.15);
    border-radius: 4px;
    transition: all 0.3s ease;
  }
  .wrimagecard .fa,
  .wrimagecard .bx {
    position: relative;
    font-size: 70px;
  }
  .wrimagecard-topimage_header {
    padding: 20px;
  }
  a.wrimagecard:hover,
  .wrimagecard-topimage:hover {
    box-shadow: 2px 4px 8px 0px rgba(46, 61, 73, 0.2);
    cursor: pointer;
    background-color: #ebefe0;
  }
  .wrimagecard-topimage a {
    width: 100%;
    height: 100%;
    display: block;
  }
  .wrimagecard-topimage_title {
    padding: 8px 24px;
    height: 80px;
    position: relative;
  }
  .wrimagecard-topimage a {
    border-bottom: none;
    text-decoration: none;
    color: #525c65;
    transition: color 0.3s ease;
  }
</style>
@endpush

<v-container id="menu">
    <v-row id="main-menu" class="mt-2">
    <v-col md="4" sm="8" v-for="item in menuItems" :key="item.value">
        <div
        class="wrimagecard wrimagecard-topimage"
        @click="changeCurrent(item.value)"
        >
        <div class="wrimagecard-topimage_header">
            <center>
            <v-icon
                class="menu-icon"
                x-large
                color="var(--secondary-color)"
                >@{{ item.icon }}</v-icon
            >
            </center>
        </div>
        <div class="wrimagecard-topimage_title">
            <h6>@{{ item.title }}</h6>
        </div>
        </div></v-col
    >
    </v-row>
</v-container>

<script type="module">
  var menu = new Vue({
    el: '#menu',
    vuetify: new Vuetify(),
    data: {
      menuItems: []
    },
    created(){
      this.loadMenu();
    },
    methods: {
      loadMenu() {
        axios.get("{{route('menu.get')}}")
          .then(response => {
            this.menuItems = response.data;
          })
          .catch(error => console.log(error.response.data));
      },
      changeCurrent(value) {
        this.$emit("clicked", value);
      },
    },
  });
</script>