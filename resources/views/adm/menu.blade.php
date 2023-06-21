@php
    
@endphp

@extends('layout.main')

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

@section("content")
@include('layout.navbar')
<v-app id="menu">
    <v-main>
        <v-container>
            <v-row id="main-menu" class="mt-2">
              <v-col md="4" sm="8" v-for="item in menuItems" :key="item.value">
                <div
                  class="wrimagecard wrimagecard-topimage"
                  @click="handleClick(item.value, item.redirect)"
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
    </v-app>
</v-main>
@endsection

@push("scripts")
<script>
    var menu = new Vue({
        el: '#menu',
        vuetify: new Vuetify(),
        data: {
            menuItems: [],
            navList: {
                "adm": [
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
            }
        },
        mounted(){
            this.loadMenu();
        },
        methods: {
            loadMenu() {
                let user_type = sessionStorage.getItem("user_type");
                if (user_type == 1) this.menuItems = this.navList.adm;
                else this.menuItems = this.navList.tutor;
            },
            handleClick(value, redirect) {
                sessionStorage.setItem("group", value);
                window.location = redirect;
            },
        },
    });
</script>
@endpush