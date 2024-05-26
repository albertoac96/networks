<template>
    <v-navigation-drawer app permanent width="350" >


        <v-tabs v-model="tab" fixed-tabs background-color="indigo" dark>
          
            <v-tab>
                Compute Graph
            </v-tab>

        </v-tabs>

        <v-tabs-items v-model="tab">
            
            <v-tab-item>
                <compute :info="info" :singleTable="singleTable"></compute>
               
                
            </v-tab-item>
        </v-tabs-items>

    </v-navigation-drawer>
</template>
<script>
import info from "./pinfo.vue";
import compute from './compute.vue';
export default {
    name: "",
    props: ["info", "singleTable"],
    components: {
        info,
        compute
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