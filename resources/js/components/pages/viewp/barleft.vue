<template>
    <v-navigation-drawer 
    v-model="$store.state.menu.drawer" 
    :mini-variant.sync="$store.state.menu.mini" 
    app 
    width="350" 
    floating >

        <v-list-item class="px-2">
        <v-list-item-avatar>
          <v-img src="https://randomuser.me/api/portraits/men/85.jpg"></v-img>
        </v-list-item-avatar>

        <v-list-item-title>Compute Graph</v-list-item-title>

        <v-btn
          icon
          @click.stop="$store.state.menu.mini = !$store.state.menu.mini"
        >
          <v-icon>mdi-chevron-left</v-icon>
        </v-btn>
      </v-list-item>

      <v-divider></v-divider>

        <!--<v-tabs v-model="$store.state.tabLeft" fixed-tabs background-color="indigo" dark>
          
            <v-tab>
                Graph Layers
            </v-tab>

            <v-tab>
                Compute graph
            </v-tab>

        </v-tabs>

        <v-tabs-items v-model="$store.state.tabLeft">
            
            <v-tab-item>
           
               <grafos :info="info"></grafos>
                
            </v-tab-item>
            <v-tab-item>
                <compute :info="info" :singleTable="singleTable"></compute>
        
                
            </v-tab-item>
        </v-tabs-items>-->

        <v-tooltip right color="primary">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
        v-if="$store.state.menu.mini"
          color="primary"
          dark
          fab
          v-bind="attrs"
          v-on="on"
        >
        <v-icon dark>
        mdi-plus
      </v-icon>
        </v-btn>
      </template>
      <span>Compute Graph</span>
    </v-tooltip>

        <compute v-show="!$store.state.menu.mini" :info="info" :singleTable="singleTable"></compute>

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

</style>