<template>
    <div class="text-center">
      <v-dialog
        v-model="dlgCompute"
        width="500"
        persistent
      >
       
  
        <v-card>
          <v-card-title class="text-h5 grey lighten-2">
            New Compute Graph
          </v-card-title>

          <v-col
          cols="12"
        >
          <v-text-field
            v-model="cg.nameGrafo"
            label="Name"
            required
          ></v-text-field>
        </v-col>
  
          <v-col
            cols="12"
            >
            <h5>Network Type</h5>
        

            <v-radio-group
                v-model="cg.netType"
                
                
                >
                <v-radio
                v-for="item in models" :key="item.id"
                    :label="item.name"
                    :value="item.id"
                ></v-radio>
              
            </v-radio-group>

<v-row>
    <v-flex xs1></v-flex>
            <v-flex xs4>
            <v-text-field
            :disabled="disBeta"
            label="Beta"
              type="number"   
              step="any"
              min="0"
              ref="input"
              :rules="[numberRule]"
              v-model="cg.beta"
            ></v-text-field>
          </v-flex>

          <v-flex xs2></v-flex>

          <v-flex xs4>
            <v-text-field
            :disabled="disSigma"
            label="Sigma"
              type="number"   
              step="any"
              min="0"
              ref="input"
              :rules="[numberRule]"
              v-model="cg.sigma"
            ></v-text-field>
          </v-flex>
        </v-row>
        <v-flex xs1></v-flex>


            </v-col>



          
            <v-col
            cols="12"
            >
            <h5>Distance funtion</h5>
            <v-radio-group
                v-model="cg.distFuntion"
                row
                
                >
                <v-radio
                v-for="item in distance" :key="item.id"
                    :label="item.name"
                    :value="item.id"
                ></v-radio><br>
              
            </v-radio-group>
            </v-col>

          
  
          <v-divider></v-divider>
  
          <v-card-actions>
          
        
                <v-btn color="success" @click="fnProccess()">Proccess</v-btn>
          
            <v-spacer></v-spacer>
           
                <v-btn color="error" @click="$emit('close')">Cancel</v-btn>
           
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </template>



<script>

export default {
    name: "",
    props: ["info", "singleTable", "dlgCompute"],
    components: {

    },
    data: () => ({
        tab: null,
        dialog: true,
        models:[ 
            { name: "Voronoi Diagram", id: "vd" },
            { name:  "Delauney Triangulation", id: "dt" },
            { name: "Gabriel Graph", id: "gg" }, 
            { name: "Beta Skeleton", id: "bs" }, 
            { name: "Relative Neihbourhood Graph", id: "rng" },
            { name: "Limited Neihbourhood Graph", id: "lng" } 
        ],
        distance: [
            {name: "Haversine in Kms", id: "hk" },
            {name: "Haversine in Milles", id: "hm"}, 
            {name: "Euclidean", id: "e" }
        ],
        cg: {
            beta: "1",
            sigma: "0",
            info: null,
            distFuntion: "hk",
            netType: null,
            singleTable: [],
            nameGrafo: ""
        },
        selectModel: null,
        selectDis: "hk",
        disSigma: true,
        disBeta: true,
  
        numberRule: val => {
            if(val < 0) return 'Please enter a positive number'
            return true
        }
    }),
    mounted() {
        console.log(this.singleTable);
    },
    created() {

    },
    beforeMount() {

    },
    watch: {
        'cg.netType'(val){
            switch(val){
                case'vd':
                    this.disSigma=true;
                    this.disBeta=true;
                    this.cg.beta=1;
                    this.cg.sigma=0;
                    break;
                case'dt':
                    this.disSigma=true;
                    this.disBeta=true;
                    this.cg.beta=1;
                    this.cg.sigma=0;
                    break;
                case'gg':
                    this.disSigma=true;
                    this.disBeta=false;
                    this.cg.beta=1;
                    this.cg.sigma=0;
                    break;
                case'bs':
                    this.disSigma=true;
                    this.disBeta=false;
                    this.cg.beta=1;
                    this.cg.sigma=0;
                    break;
                case'rng':
                    this.disSigma=true;
                    this.disBeta=false;
                    this.cg.beta=2;
                    this.cg.sigma=0;
                    break;
                case'lng':
                    this.disSigma=false;
                    this.disBeta=false;
                    this.cg.beta=1;
                    this.cg.sigma=0;
                    break;


            }
        },
        'cg.singleTable'(val){
            //this.cg.singleTable = val;
        }
    },
    computed: {

    },
    methods: {
        fnProccess(){
            this.cg.info = this.$store.state.infoProyecto;
            this.cg.singleTable = this.singleTable;
            console.log(this.cg);
            axios
              .post("/projects/compute", this.cg)
              .then((res) => {
                this.$store.state.selectGraph = res.data;
                this.$store.state.singleTable = res.data.nodes;
            })
            .catch((error) => {
            });
        },
        fnNodeControl(){
            console.log(this.$store.state.selectGraph);
            axios
              .post("/projects/controlnode", this.$store.state.selectGraph)
              .then((res) => {
                for(var i=0; i<res.data.controlValuesArray.length; i++){
                    this.$store.state.singleTable[i].ControlValue = res.data.controlValuesArray[i];
                    this.$store.state.singleTable[i].RelativeAssymetry = res.data.relativeAssymmetry[i];
                    this.$store.state.meanControl = res.data.meanControl;
                }
            })
            .catch((error) => {
            });
        }
    }
}
</script>