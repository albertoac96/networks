<template>

    <v-card>
        <v-card-title>
            Projects
            <v-divider
                class="mx-4"
                inset
                vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              dark
              class="mb-2"
              to="/newp"
            >
              New
            </v-btn>
        </v-card-title>
        <v-card-text>
            <v-data-table
                :headers="headers"
                :items="desserts"
                :items-per-page="5"
                class="elevation-1  table-striped"
                @dblclick:row="viewItem"
                
            >
          
            <template v-slot:item.created_at="{ item }" >
                
              {{ cFechaYMDlarga(item.created_at) }}


          </template>

          <template v-slot:item.updated_at="{ item }" >
                
                {{ cFechaYMDlarga(item.updated_at) }}
  
  
            </template>
          
          
          
          </v-data-table>
        </v-card-text>

</v-card>
    
    </template>
    <script>

    export default {
        name: "",
        props: [],
        components:{
           
        },
        data: () => ({
            headers: [
          {
            text: 'Name',
            align: 'start',
            sortable: false,
            value: 'cName',
          },
          
          { text: 'Description', value: 'cDescription' },
          { text: 'Created at', value: 'created_at' },
          { text: 'Last modified', value: 'updated_at' },
        ],
        desserts: []
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
          }
        }
    }
    </script>