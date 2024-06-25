<template>


  <v-navigation-drawer app right width="250" color="blue-grey lighten-5">
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
    
  </v-navigation-drawer>


</template>
<script>

export default {
    name: "",
    props: [],
    components: {

    },
    data: () => ({
        tab: null,
       rango: [-20, 70],
       color: "#FF00FFFF"
    }),
    mounted() {

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