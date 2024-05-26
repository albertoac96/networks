<template>

<v-tooltip bottom v-if="!$store.state.menu.drawerR">
      <template v-slot:activator="{ on, attrs }">
        <v-icon
        
          color="white"
          dark
          v-bind="attrs"
          v-on="on"
          @click.stop="$store.state.menu.drawerR = true"
        >
         mdi-menu-open
        </v-icon>
      </template>
      <span>Abrir panel de detalles</span>
    </v-tooltip>

    <v-tooltip bottom v-else>
      <template v-slot:activator="{ on, attrs }">
        <v-icon
        
          color="white"
          dark
          v-bind="attrs"
          v-on="on"
          @click.stop="$store.state.menu.drawerR = false"
        >
         mdi-menu-right
        </v-icon>
      </template>
      <span>Cerrar panel de detalles</span>
    </v-tooltip>


</template>
<script>
export default {
    name: "",
    props: [],
    components:{

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
}
</script>