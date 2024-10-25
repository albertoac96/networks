<template>
  <v-app>
   
   
    <!-- Sizes your content based upon application components -->
    <v-main>

     

      <barraup></barraup>
      <!-- Provides the application the proper gutter -->
      <v-container fluid>

        <v-overlay :value="$store.state.overlay" :z-index="40000">
      <v-progress-circular
        indeterminate
        size="64"
        color="black"
      ></v-progress-circular>
    </v-overlay>
        <!-- If using vue-router -->
       
            <router-view></router-view>

        
      </v-container>
    </v-main>

    <v-footer app>
      <!-- -->
    </v-footer>
  </v-app>
</template>

<script>

import barraup from './barraup.vue';
import snack from './snackbar.vue';
import NodeNetwork from '../pages/viewp/network_animation.vue';

export default {
   name: "diego",
    props: [],
    components:{
      snack,
      barraup,
      NodeNetwork
    },
    data: () => ({
      overlay: false
    }),
    mounted(){
      this.removeLoadingOverlay();
    },
    created(){
        
    },
    beforeMount(){

    },
    watch:{
        
    },
    computed:{
   
    },
    methods:{
      removeLoadingOverlay() {
      const overlay = document.getElementById('loading-overlay');
      if (overlay) {
        overlay.style.display = 'none';
      }
    }
    }
};
</script>

<style scoped>


#app {
  background-color: #f0f4f8; /* Elige el color que prefieras */
  min-height: 100vh; /* Asegúrate de que el fondo cubra toda la pantalla */
  font-family: 'Microsoft Sans Serif', sans-serif;
}
</style>



