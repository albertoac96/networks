<template>
    <v-navigation-drawer app right width="250" color="blue-grey lighten-5">

        <v-tabs
  v-model="tab"
  
    background-color="indigo"
    dark
  >
    <v-tab>
      MAP LEGEND
    </v-tab>

    
  </v-tabs>

  <v-tabs-items v-model="tab">
      <v-tab-item>
        <v-card flat>
                    <v-card-text>LEGENDAS</v-card-text>
                </v-card>
      </v-tab-item>
   
     
    </v-tabs-items>

    


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