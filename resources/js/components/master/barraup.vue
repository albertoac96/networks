<template>


<v-system-bar 
  height="30"
  color="grey darken-3"
  elevation="2"
  app
  window
>

    

<v-menu offset-y>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="primary"
          dark
          dense
          v-bind="attrs"
          v-on="on"
        >
          Project
        </v-btn>
      </template>
      <v-list dense>
        <v-list-item>
          <v-list-item-title>About</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-title>Return to my projects</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>

    
<v-menu offset-y>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="primary"
          dark
          dense
          v-bind="attrs"
          v-on="on"
        >
          Functions
        </v-btn>
      </template>
      <v-list dense>
        <v-list-item>
          <v-list-item-title>Compute Graph</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-title>Calculate Nodes Control</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-title>Calculate Relative Assymetry</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
        

<v-spacer></v-spacer>

<menuPerfil></menuPerfil>

<full></full>
 

</v-system-bar>

 
</template>


<script>
import full from '../menu/fullscreen.vue';
import seeBarraDerecha from '../menu/seeBarraDerecha.vue';
import menuPerfil from '../menu/menuPerfil.vue';
export default {
  name: "menuTop",
  data: () => ({
      items: [
        {
          text: 'Mis proyectos',
          disabled: false,
          href: '/',
        }
      
      ],
    }),
  components:{
      full,
      seeBarraDerecha,
      menuPerfil
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
        }
  }
  
};
</script>


<style scoped>
.white-text {
  color: white !important; /* Usa !important para asegurarte de que los estilos se apliquen */
}
</style>