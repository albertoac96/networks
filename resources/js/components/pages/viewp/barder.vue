<template>




<div v-show="$store.state.drawer" class="drawer">

  <v-btn v-show="$store.state.drawer" icon @click="$store.state.drawer = false" color="pink" >
      <v-icon>mdi-close</v-icon>
    </v-btn>

    <div class="scrollable-div">

    <layers></layers>

  <v-col class="px-4">
    <v-slider
      v-model="$store.state.stylePuntos.radio"
      :max="25"
      :min="0"
      hide-details
      class="align-center"
      label="Radio"
    ></v-slider>
  </v-col>

  <v-col class="px-4">
    <v-slider
      v-model="$store.state.stylePuntos.weight"
      :max="10"
      :min="0"
      hide-details
      class="align-center"
      label="Borde"
    ></v-slider>
  </v-col>


  <v-col class="px-4">
  <v-color-picker
      v-model="$store.state.stylePuntos.color"
      class="ma-2"
      hide-inputs
      label="Borde"
    ></v-color-picker>
    </v-col>

    <v-col class="px-4">
  <v-color-picker
      v-model="$store.state.stylePuntos.fillcolor"
      class="ma-2"
      hide-inputs
      label="Relleno"
    ></v-color-picker>
    </v-col>
    
  </div>

</div>


</template>
<script>
import layers from "./grafos.vue";
export default {
    name: "",
    props: [],
    components: {
      layers
    },
    data: () => ({
        tab: null,
       rango: [-20, 70],
       color: "#FF00FFFF"
    }),
    mounted() {
    
    },
    beforeDestroy() {
    
    },
    created() {

    },
    beforeMount() {

    },
    watch: {

    },
    computed: {

    },
    methods: {
     
    }
}
</script>

<style>
.elevation-3 {
  box-shadow: 2px 2px 6px rgba(0,0,0,0.24);
  z-index: 10000; /* Asegúrate de que el z-index sea suficientemente alto para sobresalir sobre otros componentes */
}
.v-overlay {
  opacity: 0 !important; /* Hace el overlay completamente transparente */
}
.drawer {
  width: 450px;
  position: fixed;
  top: 110px;
  right: 0;
  height: 100%;
  background: white;
  transition: transform 0.3s ease;
  transform: translateX(100%);
  box-shadow: -2px 0 8px rgba(0,0,0,0.1);
  transform: translateX(0);
  z-index: 10000; /* Asegúrate de que el z-index sea suficientemente alto para sobresalir sobre otros componentes */
}
.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease-in-out;
}

.slide-enter, .slide-leave-to {
  transform: translateX(100%);
}

.scrollable-div {
  width: 100%;        /* Toma todo el ancho disponible */
  height: 80%;      /* Altura fija */
  overflow-y: scroll; /* Solo permite scroll vertical */
  border: 1px solid #ccc;
  padding: 10px;
}


</style>
