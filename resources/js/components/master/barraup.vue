<template>


<v-system-bar 
  height="40"
  color="grey darken-3"
  style="z-index: 20000;"
  app
  window
>

<img
              src="/storage/assets/p2_icon_small.png"
              alt="icon"
              style="width: 30px; height: 30px;"
            />
            <span class="ml-2" style="color: white; font-family: 'Maven Pro', sans-serif; font-weight: 700;">RNG Special Network Analysis</span>




<v-breadcrumbs :items="items" divider="-" class="breadcrumb-item">
    <template v-slot:item="{ item }">
      <v-breadcrumbs-item :to="item.to">
        <span class="ml-2" style="color: white; font-family: 'Maven Pro', sans-serif; font-weight: 700;">{{ item.text }}</span>
      </v-breadcrumbs-item>
    </template>
  </v-breadcrumbs>

  <v-spacer></v-spacer>
<menuPerfil></menuPerfil>

<!--<v-btn v-if="$store.state.drawer" icon @click="$store.state.drawer = false">
      <v-icon>mdi-palette</v-icon>
    </v-btn>
    <v-btn v-else icon @click="$store.state.drawer = true">
      <v-icon>mdi-palette</v-icon>
    </v-btn>-->
 
<compute @close="dlgCompute=false" :dlgCompute="dlgCompute"></compute>
</v-system-bar>

 
</template>


<script>
import full from '../menu/fullscreen.vue';
import seeBarraDerecha from '../menu/seeBarraDerecha.vue';
import menuPerfil from '../menu/menuPerfil.vue';
import compute from '../pages/viewp/modalCompute.vue';
export default {
  name: "menuTop",
  data: () => ({
      items: [
        {
          text: 'Go to Datasets',
          disabled: false,
          to: '/',
        }
      
      ],
      dlgCompute: false
    }),
  components:{
      full,
      seeBarraDerecha,
      menuPerfil,
      compute
  },
  methods:{
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
        },
    compute(){
      console.log("Computar");
      this.dlgCompute = true;
    }
  }
  
};
</script>


<style scoped>
.white-text {
  color: white !important; /* Usa !important para asegurarte de que los estilos se apliquen */
}
.custom-breadcrumbs .v-breadcrumbs__item,
.custom-breadcrumbs .v-breadcrumbs__link {
  color: white;  /* Cambia el color a blanco */
  text-decoration: underline;  /* Asegura que el texto esté subrayado */
}

.breadcrumb-item {
  color: white !important; 
  font-family: 'Maven Pro', sans-serif; 
  font-weight: 700; 
  text-decoration: underline;
}

</style>