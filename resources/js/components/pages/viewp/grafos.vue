<template>
    <div>

        <h4>Graphs Layers</h4>
        <v-subheader>Total: {{ $store.state.grafos.length }}</v-subheader>

      
        <v-list
      subheader
      two-line
      flat
    >
      

      <v-list-item-group
        v-model="settings"
        multiple
      >
      <v-list-item v-for="(grafo, idGrafo) in $store.state.grafos" :key="idGrafo">
         
            <v-list-item-action v-if="grafo.cContenido!=null">
              <v-checkbox
                :input-value="grafo.active"
                @change="toggleGrafo(grafo, $event)"
                color="primary"
              ></v-checkbox>
            </v-list-item-action>

            <v-list-item-content @click="verGrafo(grafo)">
              <v-list-item-title>{{ grafo.cNombre }}</v-list-item-title>
              <v-label>{{verInfo(grafo)}}</v-label>
            </v-list-item-content>
      

         
        <v-list-item-action>
        

          <v-menu offset-y>
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon
          v-bind="attrs"
          v-on="on"
        >
        <v-icon color="grey lighten-1">mdi-information</v-icon>
        </v-btn>
      </template>
      <v-list flat>
        <v-list-item>
            <v-list-item-title>Calculate Nodes Control</v-list-item-title>
        </v-list-item>
        <v-list-item>
            <v-list-item-title>Calculate Relative Assimetry</v-list-item-title>
        </v-list-item>
        <v-list-item>
            <v-list-item-title>More information</v-list-item-title>
        </v-list-item>
        <v-list-item>
            <v-list-item-title>Download</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
         
        </v-list-item-action>
        </v-list-item>

     </v-list-item-group>
        </v-list>

    </div>

</template>
<script>

export default {
    name: "verGrafos",
    props: [],
    components: {

    },
    data: () => ({
        grafos:[],
        settings: []
    }),
    mounted() {
       
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
        toggleGrafo(grafo, event){
            console.log("ENTRE A TOGGLE");
            console.log(grafo);
            var contenido = JSON.parse(grafo.cContenido);
                console.log(contenido);
                console.log(this.$store.state.grafos);
            if(event === true){
                this.$store.state.selectGraphs.push(contenido.geo);
            } else {
                const index = this.$store.state.selectGraphs.findIndex(item => item.id == contenido.geo.id);
                console.log(contenido.geo.id);
                console.log(index);
                this.$store.state.selectGraphs.splice(index, 1);
            }
            console.log(this.$store.state.selectGraphs);
        },
        verInfo(grafo){
            var contenido = JSON.parse(grafo.cContenido);
            var netType = contenido.netType;
            var distFunction = contenido.distFunction;
            switch (netType) {
                case "vd":
                    netType = "Voronoi Diagram";
                    break;
                case "dt":
                    netType = "Delauney Triangulation";
                    break;
                case "gg":
                    netType = "Gabriel Graph";
                    break;
                case "bs":
                    netType = "Beta Skeleton";
                    break;
                case "rng":
                    netType = "Relative Neihbourhood Graph";
                    break;
                case "lng":
                    netType = "Limited Neihbourhood Graph";
                    break;
                default:
                    netType = "Unknown Network Type";
                    break;
            }
            switch (distFunction) {
                case "hk":
                    distFunction = "Haversine in Kms";
                    break;
                case "hm":
                    distFunction = "Haversine in Milles";
                    break;
                case "e":
                    distFunction = "Euclidean";
                    break;
                default:
                    distFunction = "Unknown Dist Function";
                    break;
            }
            var LcResp = netType + " - (" + distFunction + ")" + "| Beta = " + contenido.nBeta + " ; Sigma = " + contenido.nSigma ;
            return LcResp;

        },
        verGrafo(grafo){
            console.log(grafo);
            var contenido = JSON.parse(grafo.cContenido);
            console.log(contenido);
            this.$store.state.selectGraph = contenido;
            this.$store.state.idGrafo = grafo.idGrafo;
        }
    }
}
</script>