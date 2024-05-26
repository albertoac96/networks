<template>
  <v-app>
      <barraup></barraup>
    
    
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
