<template>


  
    <v-card class="rounded-xl m-3">

   


        <v-card-title style="font-family: 'Nunito', sans-serif; font-weight: 700;" class="d-flex align-center">
        
         
          <span>Datasets</span>
          <v-divider class="mx-4" inset vertical dense></v-divider>
      
          
            <v-spacer></v-spacer>

            <div class="col-2 mt-4">
                <v-text-field
                v-model="search"
                outlined
                    dense
                  color="#2B968A"
                    label="Search"
                ></v-text-field>
              </div>
          
            <v-btn
              color="#2B968A"
              dark
              dense
              @click="addDataset()"
              class="d-flex align-center rounded-lg"
            >
            <img
              src="storage/assets/p2_newproject.png"
              alt="icon"
              style="width: 20px; height: 20px; rounded"
            />
            <span class="ml-2">New dataset</span>
            </v-btn>
         
        </v-card-title>
        <v-card-text>
          <v-container style="width: 80%;">
            <v-data-table
                fixed-header
                height="65vh"
                :headers="headers"
                :items="desserts"
                :search="search"
                :items-per-page="-1"
                hide-default-footer
                item-class="custom-row"
                item-key="idProject" 
                class="elevation-1 table-striped"
                @dblclick:row="viewItem"
              
            >

          
            <template v-slot:item.created_at="{ item }" >
                
              {{ cFechaYMDlarga(item.created_at) }}


          </template>

          <template v-slot:item.updated_at="{ item }" >
                
                {{ cFechaYMDlarga(item.updated_at) }}
  
  
            </template>
          
          
          
          </v-data-table>
        </v-container>
        </v-card-text>

       <agregardata :dialog="dialog" @cancel-dialog="dialog = false"></agregardata>

</v-card>


    </template>
    <script>
import agregardata from './newp.vue';
    export default {
        name: "",
        props: [],
        components:{
          agregardata
        },
        data: () => ({
            headers: [
          {
            text: 'Name',
            align: 'start',
            sortable: false,
            value: 'cName',
            class: 'my-header-style rounded-tl-lg mt-4 mb-4 dark'
          },
          
          { text: 'Description', value: 'cDescription', class: 'my-header-style mt-4 mb-4 dark', },
          { text: 'Graphs', value: 'grafos_count', class: 'my-header-style mt-4 mb-4 dark', },
          { text: 'Created at', value: 'created_at', class: 'my-header-style rounded-tr-lg mt-4 mb-4 dark', }
      
        ],
        desserts: [],
        dialog: false,
        search: ''
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
              .get("/projects/list")
              .then((res) => {
                this.desserts = res.data;
            })
            .catch((error) => {
            });
          },
          viewItem(event, {item}){
            console.log(item);
            this.$store.state.info = item;
            this.$router.push('/verp/' + item.idProject);
          },
          addDataset(){
            this.dialog = true;
          }
        }
    }
    </script>

    <style>


.my-header-style {
  background: #43C2B4 !important;
  text-align: center !important;
  color: black !important;
  font-size: 1em !important;
  font-family: 'Microsoft Sans Serif', sans-serif !important;
}

  </style>