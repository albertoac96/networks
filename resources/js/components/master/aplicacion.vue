<template>
  <v-app>
    <NodeNetwork v-if="$store.state.overlay" class="above-all2"></NodeNetwork>
    <snack class="above-all"></snack>
      <barraup class="above-all"></barraup>
    
    
    <!-- Sizes your content based upon application components -->
    <v-main>
      <!-- Provides the application the proper gutter -->
      <v-container fluid>
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
.above-all {
  position: fixed; /* or absolute, fixed, or sticky depending on your layout */
  z-index: 900; /* A high value to ensure it's above other elements */
}

.above-all2 {
  position: fixed; /* or absolute, fixed, or sticky depending on your layout */
  z-index: 9000; /* A high value to ensure it's above other elements */
}
</style>



