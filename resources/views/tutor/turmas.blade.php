@php
    
@endphp

@extends('layout.main')

@push("header")
<style type="text/css">
    .card-turma:hover {
      cursor: pointer;
    }
</style>
@endpush

@section("content")
@include('layout.navbar')
<v-app id="turmas">
    <v-main>
        <v-container>
        <v-row id="minhas-turmas" class="mt-2">
            <v-col offset-md="2" md="8" sm="12">
            <v-card outlined>
                <v-card-title>
                <h4>Turmas</h4>
                </v-card-title>
                <v-card-subtitle>
                <v-row class="mt-2" v-for="item in turmas" :key="item.semestre.code">
                    <v-col sm="12">
                    <h6>@{{ item.semestre.code }}</h6>
                    <v-divider/>
                    </v-col>
                    <v-col
                    md="6"
                    sm="6"
                    v-for="turma in item.semestre.turmas"
                    :key="turma.turma_id"
                    @click="openTurma(turma.turma_id)"
                    >
                    <v-card max-width="344" class="mt-2 card-turma" outlined>
                        <v-card-title style="background-color: #696969">
                        <p style="color: #ffffff">@{{turma.disciplina_code}} - @{{turma.disciplina_name}}</p>
                        </v-card-title>
                        <v-list-item three-line>
                        <v-list-item-content>
                            <v-list-item-title class="text-h6 mb-1">
                            @{{turma.turma_code}} @{{turma.class_days}}: @{{turma.class_time}}
                            </v-list-item-title>
                        </v-list-item-content>
                        </v-list-item>
                    </v-card>
                    </v-col>
                </v-row>
                </v-card-subtitle>
            </v-card>
            </v-col>
        </v-row>
        </v-container>
    </v-app>
</v-main>
@endsection

@push("scripts")
<script>
    var turmas = new Vue({
        el: '#turmas',
        vuetify: new Vuetify(),
        data: {
            turmas: []
        },

        created(){
            this.getTurmas();
        },

        methods: {
            getTurmas(){
                axios.get("{{route('turma-tutor.turmas')}}")
                    .then(response => {
                        this.groupBy(response.data, semestre => semestre.semestre_code).forEach((turmas, index) => {
                            this.turmas.push({ semestre: {
                                code: index,
                                turmas: turmas
                            }});
                        });
                    })
                    .catch(error => console.log(error.response.data))
            },
            openTurma(turma_id){
                window.location = "{{route('tutor.turma', 'id')}}".replace('id', turma_id);
            },

            groupBy(list, keyGetter) {
                const map = new Map();
                list.forEach((item) => {
                    const key = keyGetter(item);
                    const collection = map.get(key);
                    if (!collection) {
                        map.set(key, [item]);
                    } else {
                        collection.push(item);
                    }
                });
                return map;
            }
        },
    });
</script>
@endpush