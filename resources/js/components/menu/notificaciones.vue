<template>




    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :nudge-width="200"
      offset-x
    >
      <template v-slot:activator="{ on: onMenu }">
        <v-tooltip bottom  v-on="onMenu">
      <template v-slot:activator="{ on: onTooltip }">
        <v-icon
        
          color="white"
          dark
          v-on="{ ...onMenu, ...onTooltip }"
         
        >
         mdi-bell-outline
        </v-icon>
      </template>
      <span>Notificaciones</span>
    </v-tooltip>
      </template>

      <v-card>
        <v-list>
          <v-list-item>
            <v-list-item-avatar>
              <img
                src="https://cdn.vuetifyjs.com/images/john.jpg"
                alt="John"
              >
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>John Leider</v-list-item-title>
              <v-list-item-subtitle>Founder of Vuetify</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn
                :class="fav ? 'red--text' : ''"
                icon
                @click="fav = !fav"
              >
                <v-icon>mdi-heart</v-icon>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list>
          <v-list-item>
            <v-list-item-action>
              <v-switch
                v-model="message"
                color="purple"
              ></v-switch>
            </v-list-item-action>
            <v-list-item-title>Enable messages</v-list-item-title>
          </v-list-item>

          <v-list-item>
            <v-list-item-action>
              <v-switch
                v-model="hints"
                color="purple"
              ></v-switch>
            </v-list-item-action>
            <v-list-item-title>Enable hints</v-list-item-title>
          </v-list-item>
        </v-list>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
            text
            @click="menu = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            text
            @click="menu = false"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>


</template>
<script>
export default {
    name: "",
    props: [],
    components:{

    },
    data: () => ({
        fav: true,
      menu: false,
      message: false,
      hints: true,
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