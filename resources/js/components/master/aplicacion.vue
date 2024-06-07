<template>
  <v-app>
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
export default {
   name: "diego",
    props: [],
    components:{
      
      barraup
    },
    data: () => ({
     
    }),
    mounted(){
     
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
      
    }
};
</script>

<style scoped>
.above-all {
  position: relative; /* or absolute, fixed, or sticky depending on your layout */
  z-index: 1000; /* A high value to ensure it's above other elements */
}
</style>

