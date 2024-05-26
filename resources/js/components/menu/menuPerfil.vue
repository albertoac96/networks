<template>
      <div class="text-center">
    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :nudge-width="200"
      offset-x
    >
      <template v-slot:activator="{ on }">
       <v-btn icon x-large v-on="on">
          <v-avatar color="white" size="27">
            <img
            
              src="https://i.pinimg.com/280x280_RS/2e/45/66/2e4566fd829bcf9eb11ccdb5f252b02f.jpg"
              alt="John"
            />
           
          </v-avatar>
        </v-btn>
      </template>

      <v-card>
        <v-list>
          <v-list-item>
            <v-list-item-avatar>
              <img
                src="https://i.pinimg.com/280x280_RS/2e/45/66/2e4566fd829bcf9eb11ccdb5f252b02f.jpg"
                alt="John"
              >
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>{{user}}</v-list-item-title>
              <v-list-item-subtitle>{{mail}}</v-list-item-subtitle>
            </v-list-item-content>

           
          </v-list-item>
        </v-list>

        <v-divider></v-divider>

      
         
        

        <v-card-actions>
          <v-spacer></v-spacer>

          
          <v-btn
            color="primary"
            text
            @click="logout()"
          >
            Salir
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
  </div>
</template>
<script>
export default {
    name:"menuPerfil",
     data: () => ({
      fav: true,
      menu: false,
      message: false,
      hints: true,
      user: "",
      mail: "",
    }),
    mounted(){
      axios
           .get("/infou")
           .then((res) => {
              
              this.user = res.data.name;
              this.mail = res.data.email;
           })
           .catch((error) => {
      });
    },
    methods:{
      logout() {
      axios
        .post("/logout")
        .then(function (response) {
          location.reload();
        })
        .catch(function (error) {
          var errors = error.response;
          if (errors.statusText === "Unprocessable Entity") {
          }
        });
        
    },
    
    }
}
</script>