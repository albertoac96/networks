<template>

<v-navigation-drawer
    
      app
      
    overlay-opacity="50%"
      width="350"
      color="blue-grey lighten-5"
      src="http://tectonicablog.com/wp-content/uploads/2010/10/teozacoalco.jpg"
   
    >
     <v-list-item class="px-2" >
        <!--<v-list-item-avatar>
          <v-img src="https://randomuser.me/api/portraits/men/85.jpg"></v-img>
        </v-list-item-avatar>-->

        <v-list-item-title>
          <v-img
          class="mt-5 mb-5"
          src="/images/config/logolema.png"></v-img>
        </v-list-item-title>

        <v-btn icon @click.stop="$store.state.menu.drawer = !$store.state.menu.drawer">
          <v-icon v-if="!mini">mdi-chevron-left</v-icon>
           <v-icon v-if="mini">mdi-dots-vertical</v-icon>
        </v-btn>
      </v-list-item>

     

   
     
    </v-navigation-drawer>



</template>
<script>

export default {
    name: "",
    props: [],
    components:{
        
    },
    data: () => ({
        drawer: true,
        mini: false,
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