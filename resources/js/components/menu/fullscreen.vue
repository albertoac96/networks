<template>
<div>


    <v-tooltip bottom v-if="!fulld">
      <template v-slot:activator="{ on, attrs }">
        <v-icon
        
          color="white"
          dark
          v-bind="attrs"
          v-on="on"
         @click.stop="full()"
        >
         mdi-fullscreen
        </v-icon>
      </template>
      <span>Pantalla completa</span>
    </v-tooltip>

    <v-tooltip bottom v-else>
      <template v-slot:activator="{ on, attrs }">
        <v-icon
        
          color="white"
          dark
          v-bind="attrs"
          v-on="on"
         @click.stop="fullExit()"
        >
        mdi-fullscreen-exit
        </v-icon>
      </template>
      <span>Salir de pantalla completa</span>
    </v-tooltip>


    
</div>
</template>
<script>
export default {
    name: "fullscreen",
    props: [],
    components:{

    },
    data: () => ({
        fulld: false,
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
      fullExit(){
          this.fulld = false;
           if (document.exitFullscreen) {
            document.exitFullscreen();
        }
        else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        }
        else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
        else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
      },
       full(){
           this.fulld = true;
      var docElm = document.documentElement;
        //W3C
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
        }
        //FireFox
        else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
        }
                 // Chrome, etc.
        else if (docElm.webkitRequestFullScreen) {
            docElm.webkitRequestFullScreen();
        }
        //IE11
        else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }

    }
    }
}

 document.addEventListener("fullscreenchange", function () {
        fullscreenState.innerHTML = (document.fullscreen) ? "" : "not ";
    }, false);
 
    document.addEventListener("mozfullscreenchange", function () {
        fullscreenState.innerHTML = (document.mozFullScreen) ? "" : "not ";
    }, false);
 
    document.addEventListener("webkitfullscreenchange", function () {
        fullscreenState.innerHTML = (document.webkitIsFullScreen) ? "" : "not ";
    }, false);
 
    document.addEventListener("msfullscreenchange", function () {
        fullscreenState.innerHTML = (document.msFullscreenElement) ? "" : "not ";
    }, false);


</script>