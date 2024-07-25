<template>
    <div>

      

  <v-tooltip bottom style="z-index: 99999;">
      <template v-slot:activator="{ on, attrs }">
       
        <button v-show="!$store.state.drawer" class="floating-button" @click="toggleDrawer" v-bind="attrs"
        v-on="on">
        <v-icon>mdi-layers</v-icon>
  </button>
      </template>
      <span>View Layers</span>
    </v-tooltip>
     
      <barizq :info="proyecto" :singleTable="singleTable"></barizq>

      <barder :info="proyecto"></barder>
      

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
        headersOriginal: [],
        loading: true,
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
      toggleDrawer(){
        this.$store.state.drawer = !this.$store.state.drawer;
      },
      inicio(){

        const overlay = document.getElementById('loading-overlay');
      if (overlay) {
        overlay.style.display = 'true';
      }


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
         this.$store.state.headers.dataOriginal = this.headersOriginal;
          var nodes = res.data.table.aNodes;
          this.datos = res.data.table.aTable;
          this.singleTable = res.data.table.aSingleTable;
          this.$store.state.singleTable = this.singleTable;
         
          this.$store.state.formatedData.dataOriginal = this.datos;
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

          setTimeout(() => {
        // Aquí iría la lógica para cargar datos
        this.loading = false;
      }, 3000);

        })
        .catch(error => {});
                          
      }
    }
}
</script>

<style>
.floating-button {
  position: fixed;
  right: 0px; /* Mantén o ajusta la distancia desde el borde derecho */
  top: 300px; /* Posición a 50px por debajo del límite superior */
  z-index: 99999; /* Asegúrate de que el botón esté sobre otros elementos */
  padding: 15px;
  background-color: #CFD8DC; /* Color de fondo azul, puedes cambiarlo según tu diseño */
  border: none;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Sombra para destacar el botón */
  cursor: pointer;
  color: white; /* Cambia el color del texto/icono a blanco */
}

.floating-button img {
  width: 24px; /* Ajusta esto según el tamaño del icono deseado */
  height: auto;
  color: white;
}

</style>