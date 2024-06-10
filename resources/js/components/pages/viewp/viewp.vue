<template>
    <div>
       
      <barizq :info="proyecto" :singleTable="singleTable"></barizq>
      

        <v-card>
           
  <v-tabs
  v-model="tab"
    fixed-tabs
    background-color="indigo"
    dark
  >
    <v-tab>
      Map View
    </v-tab>
    <v-tab>
      Data
    </v-tab>
    <v-tab>
      Nodes
    </v-tab>
    <v-tab>
      Edges
    </v-tab>
    <v-tab>
      Adjacency List
    </v-tab>
    <v-tab>
      Distance Matrix
    </v-tab>
  </v-tabs>

  <v-tabs-items v-model="tab">
      <v-tab-item>
        <mapa :lugares="datos" :singleTable="singleTable"></mapa>
      </v-tab-item>
   
      <v-tab-item>
        <datos :headers="headersOriginal" :datos="datos" :singleTable="singleTable"></datos>
      </v-tab-item>

      <v-tab-item>
        <nodes :headers="headers" :datos="datos" :singleTable="singleTable"></nodes>
      </v-tab-item>

      <v-tab-item>
        <edges :headers="headers" :datos="datos"></edges>
      </v-tab-item>

      <v-tab-item>
        <adiacencia :headers="headers" :datos="adiacencia"></adiacencia>
      </v-tab-item>

      <v-tab-item>
        <distance :datos="datos"></distance>
      </v-tab-item>


    </v-tabs-items>

        </v-card>


    </div>
</template>
<script>
import barder from "./barder.vue";
import barizq from "./barleft.vue";
import mapa from "./map.vue";
import datos from "./data.vue";
import distance from "./distance.vue";
import edges from "./edges.vue";
import nodes from "./nodes.vue";
import adiacencia from "./adiacencia.vue";
export default {
    name: "",
    props: [],
    components: {
        barder, barizq, mapa, datos, distance, edges, nodes, adiacencia
    },
    data: () => ({
        tab: null,
        proyecto: null,
        headers: [],
        datos: [],
        singleTable: [],
        adiacencia: [],
        headersOriginal: []
    }),
    mounted() {
      this.inicio();
    },
    created() {

    },
    beforeMount() {

    },
    watch: {

    },
    computed: {

    },
    methods: {
      inicio(){
        var id = this.$route.params.id;
        axios
        .get("/projects/info/" + id)
        .then(res => {
        this.proyecto = res.data.info;
        this.adiacencia = res.data.adjacencyList;
        console.log(this.adiacencia);
        this.$store.state.infoProyecto = res.data.info;
        this.$store.state.grafos = res.data.grafos;
        console.log(res.data);
          var headers = res.data.table.aHeaders;
         this.headersOriginal = headers;
          var nodes = res.data.table.aNodes;
          this.datos = res.data.table.aTable;
          this.singleTable = res.data.table.aSingleTable;
          this.$store.state.singleTable = this.singleTable;
          //console.log(res.data.table.aTable);
          console.log('HEADERS')
          console.log(headers);
          console.log(nodes);
          console.log(this.datos);

          var positions = new Array();
          if(nodes.id != null){
            var pos = headers.indexOf(nodes.id);
            positions.push({name: nodes.id, index: pos});
            console.log(pos);
          }
          if(nodes.name != null){
            var pos = headers.indexOf(nodes.name);
            positions.push({name: nodes.name, index: pos});
            console.log(pos);
          }
          if(nodes.x != null){
            var pos = headers.indexOf(nodes.x);
            positions.push({name: nodes.x, index: pos});
            console.log(pos);
          }
          if(nodes.y != null){
            var pos = headers.indexOf(nodes.y);
            positions.push({name: nodes.y, index: pos});
            console.log(pos);
          }

          console.log(positions);
          this.headers = positions;
          console.log(this.datos);

        })
        .catch(error => {});
                          
      }
    }
}
</script>