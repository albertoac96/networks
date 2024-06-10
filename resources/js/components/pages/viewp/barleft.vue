<template>
    <v-navigation-drawer app permanent width="350" class="above-all" >


        <v-tabs v-model="tab" fixed-tabs background-color="indigo" dark>
          
            <v-tab>
                Graph Layers
            </v-tab>

            <v-tab>
                Compute graph
            </v-tab>

        </v-tabs>

        <v-tabs-items v-model="tab">
            
            <v-tab-item>
           
               <grafos :info="info"></grafos>
                
            </v-tab-item>
            <v-tab-item>
                <compute :info="info" :singleTable="singleTable"></compute>
        
                
            </v-tab-item>
        </v-tabs-items>

    </v-navigation-drawer>
</template>
<script>
import info from "./pinfo.vue";
import compute from './compute.vue';
import grafos from './grafos.vue';
export default {
    name: "",
    props: ["info", "singleTable"],
    components: {
        info,
        compute,
        grafos
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

<style scoped>
.above-all {

  z-index: 1000; /* A high value to ensure it's above other elements */
}
</style>