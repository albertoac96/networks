<template>
    <v-card
     height="70vh"
      color="indigo darken-2"
      flat
    >

    <v-navigation-drawer
        v-model="$store.state.drawer"
        fixed
        style="margin-top: 100px; z-index: 30000"
          floating
          height="690px"
          width="310px"
         
        >

        <v-tabs
        v-model="tabDraw"
        height="40px"
        fixed-tabs
        background-color="#F2F2F2"
        slider-color="#43C2B4"
        slider-size="4"
        active-class="my-active-tab"
      >
        <v-tab>Compute</v-tab>
        <v-tab>Graphs</v-tab>
       
      </v-tabs>

      <v-tabs-items v-model="tabDraw">
      <v-tab-item>
        <compute  :info="proyecto" :singleTable="singleTable"></compute>
      </v-tab-item>

      <v-tab-item>
        <grafos></grafos>
      </v-tab-item>
      </v-tabs-items>

        

        
        
        
        </v-navigation-drawer>



        <v-navigation-drawer
        
            v-model="$store.state.verEstilo"
            fixed
            style="margin-top: 200px; margin-left: 301px; z-index: 30000"
            floating
            height="430px"
            width="300px"
            
            >
            <div>
            

  <v-col class="px-4" dense>
    <v-slider
    v-model="borde"
      :max="10"
      :min="0"
      hide-details
      class="align-center"
      label="Border"
    ></v-slider>
  </v-col>


  <v-col class="px-4" dense>
  <v-color-picker
  v-model="color"
      class="ma-2"
      
    ></v-color-picker>
    </v-col>
    <v-card-actions dense>
    <v-spacer></v-spacer>
    <v-btn color="#43C2B4" @click="$store.state.verEstilo=false">SAVE</v-btn>
  </v-card-actions>

    
  </div>
        </v-navigation-drawer>




    <!-- Barra con contenido a la izquierda y pestañas a la derecha -->
    <v-toolbar height="50px" flat color="#43C2B4">
      <!-- Contenido a la izquierda -->
      <v-btn v-show="fncompute" icon @click="fncompute=false">
        <v-icon>mdi-arrow-left</v-icon>
      </v-btn>
      <v-btn v-show="!fncompute" icon @click="fncompute=true">
        <v-icon>mdi-arrow-right</v-icon>
      </v-btn>
      <v-toolbar-title width="200px">Zempoala 1550 prueba</v-toolbar-title>

     

      <!-- Espaciador que empuja las pestañas a la derecha -->
      <v-spacer></v-spacer>

      <!-- Pestañas a la derecha -->
      <v-tabs
        v-model="tab"
        height="50px"
        fixed-tabs
        background-color="#F2F2F2"
        slider-color="#43C2B4"
        slider-size="4"
        active-class="my-active-tab"
      >
        <v-tab>
          MAP VIEW
        </v-tab>
        <v-tab>DATA</v-tab>
        <v-tab>
          <div>
            <div>NODES</div>
            <div v-if="$store.state.selectGraph.geo" style="font-size: 9px;">{{ $store.state.selectGraph.geo.name }}</div>
          </div></v-tab>
        <v-tab>
          <div>
            <div>EDGES</div>
            <div v-if="$store.state.selectGraph.geo" style="font-size: 9px;">{{ $store.state.selectGraph.geo.name }}</div>
          </div></v-tab>
        <v-tab>
          <div>
            <div>ADJACENCY LIST</div>
            <div v-if="$store.state.selectGraph.geo" style="font-size: 9px;">{{ $store.state.selectGraph.geo.name }}</div>
          </div></v-tab>
        <v-tab>
          <div>
            <div>DISTANCE MATRIX</div>
            <div v-if="$store.state.selectGraph.geo" style="font-size: 9px;">{{ $store.state.selectGraph.geo.name }}</div>
          </div></v-tab>
      </v-tabs>

     


    </v-toolbar>

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


    
  </template>


<script>

import mapa from "./map.vue";
import datos from "./data.vue";
import distance from "./distance.vue";
import edges from "./edges.vue";
import nodes from "./nodes.vue";
import adiacencia from "./adiacencia.vue";
import compute from "./compute.vue";
import grafos from "./grafos.vue";
  export default {
    components: {
         mapa, datos, distance, edges, nodes, adiacencia, compute, grafos
    },
    data () {
      return {
        tab: null,
        tabDraw: null,
        items: [
          { title: 'Home', icon: 'mdi-view-dashboard' },
          { title: 'About', icon: 'mdi-forum' },
        ],
        proyecto: null,
        headers: [],
        datos: [],
        singleTable: [],
        adiacencia: [],
        headersOriginal: [],
        loading: true,
        grafos: false,
        fncompute: true,
        verEstilo: true,
        borde: 0, 
        color: '',
        indexGraph: null
      }
    },
    mounted() {
      this.inicio();
    },
    created() {

    },
    beforeMount() {

    },
    watch: {
      grafos(oldVal, newVal){
       
        console.log(newVal);
        this.fncompute = false;
        if(newVal == true){
          this.$store.state.drawer = false;
        } else {
          if(newVal != oldVal){
            this.$store.state.drawer = true;
          }
          
        }
        
      },
      fncompute(oldVal, newVal){
       
        console.log(newVal);
        this.grafos = false;
        if(newVal == true){
          this.$store.state.drawer = false;
        } else {
          if(newVal != oldVal){
            this.$store.state.drawer = true;
          }
          
        }
        
      },
      '$store.state.selectGraph'(){
        console.log('nuevo grafo');
        console.log(this.$store.state.selectGraph);
        const currentId = this.$store.state.selectGraph.geo.id;
        const index = this.$store.state.selectGraphs.findIndex(item => item.id === currentId);
        console.log(index);
        console.log(this.$store.state.selectGraphs);
        this.indexGraph = index;
        
        if(!this.$store.state.selectGraphs[index].properties){
          this.$store.state.selectGraphs[index].properties = {};
          this.$store.state.selectGraphs[index].properties['stroke-width'] = 2;
          this.$store.state.selectGraphs[index].properties['stroke'] = 'white';
        } 
        this.borde = this.$store.state.selectGraphs[index].properties['stroke-width'];
        this.color = this.$store.state.selectGraphs[index].properties['stroke'];
        
        
      },
      borde(){
        console.log(this.borde);
        this.$store.state.selectGraphs[this.indexGraph].properties['stroke-width'] = this.borde;
       
      }, 
      color(){
        this.$store.state.selectGraphs[this.indexGraph].properties['stroke'] = this.color;
      }
    },
    computed: {

    },
    methods:{
      inicio(){

        this.$store.state.overlay = true;

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
this.$store.state.overlay = false;
}, 0);

})
.catch(error => {});
                  
},
        clickNav(pos){
            console.log(pos);
            console.log(this.$store.state.drawer);
            if(pos == 'abrir'){
                this.$store.state.drawer = true;
            } else {
                this.$store.state.drawer = false;
            }
            
        },
        clickGrafo(pos){
          console.log(pos);
          if(pos == 'abrir'){
                this.grafos = true;
            } else {
              this.grafos = false;
            }
        }
    }
  }
</script>


<style scoped>
.v-tabs {
  background-color: #2e2e2e;
  color: white;
  
}

.v-toolbar {
  background-color: #43C2B4 !important;
  color: white;
}

.v-btn {
  color: white !important;
}

.v-avatar img {
  border-radius: 50%;
}

.v-tabs .v-tab {
  font-weight: bold;
  font-size: 14px;
  border: 1px solid #2e2e2e;
}

.my-active-tab {
  color: white !important; /* Color del texto de la pestaña activa */
  background-color: #2e2e2e;
  font-weight: bold;
  border: 2px solid #43C2B4;
}
</style>