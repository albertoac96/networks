<template>

<v-container>

    <v-card>
        <v-card-title>
            Colections of datasets
            <v-divider
                class="mx-4"
                inset
                vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-dialog
      v-model="dialog"
      width="500"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="red lighten-2"
          dark
          v-bind="attrs"
          v-on="on"
        >
          New
        </v-btn>
      </template>

      <v-card>
        <v-card-title class="text-h5 grey lighten-2">
          Add Collection
        </v-card-title>
        <v-card-text>
        <v-row>
        <v-col
          cols="12"
          
        >
          <v-text-field
            v-model="col.name"
            label="name"
            required
          ></v-text-field>
        </v-col>

        <v-col
          cols="12"
         
        >
        <v-textarea
        v-model="col.desc"
      name="input-7-1"
      filled
      label="Description"
      auto-grow
      value=""
    ></v-textarea>
        </v-col>

        </v-row>
    </v-card-text>
        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            text
            @click="addCol()"
          >
            I accept
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
        </v-card-title>
        <v-card-text>
    
    <v-row>

        <v-col
        v-for="(col, index) in cols"
        :key="index"
        cols="12" sm="4" lg="3" xl="2" 
      >


    <v-card
    class="mx-auto"
    max-width="344"
    outlined
  >

  <v-btn
        outlined
        rounded
        text
        class="mt-3 ml-3"
      >
        Eliminar
      </v-btn>


    <v-list-item three-line>

      


      <v-list-item-content>
        <v-list-item-title class="text-h5 mb-1">
          {{ col.cNombre }}
        </v-list-item-title>
        <v-list-item-subtitle>{{ col.cDescripcion }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>

    <v-card-actions>
      <v-btn
        outlined
        rounded
        text
      >
        Publico
      </v-btn>
    </v-card-actions>
  </v-card>

</v-col>


</v-row>

</v-card-text>
</v-card>
</v-container>

</template>

<script>

export default {
    name: "",
    props: [
    ],
    components:{
       
    },
    data: () => ({
       cols: [],
       col: {
        name: "",
        desc: ""
       },
       dialog: false
    }),
    mounted(){
        this.inicio();
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
     inicio(){
        axios
            .get("/cols/list")
            .then((res) => {
            this.cols = res.data;
        })
        .catch((error) => {
        });
     },
     addCol(){
        axios
            .post("/cols/add", this.col)
            .then((res) => {
            this.cols = res.data;
            this.dialog = false;
            this.col.name="";
            this.col.desc="";
        })
        .catch((error) => {
        });
     }
    }
}
</script>