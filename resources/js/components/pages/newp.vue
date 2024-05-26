<template>
    <v-row dense>
        <v-col class=".d-none .d-sm-flex" cols="3" sm="1" md="2" lg="3"></v-col>

        <v-col cols="6">
            <v-card class="justify-center mb-6">
                <v-card-title>
                    New project
                </v-card-title>
                <v-card-text>
                    <v-stepper v-model="e1">
                        <v-stepper-header>
                            <v-stepper-step
                                editable
                                :complete="e1 > 1"
                                step="1"
                            >
                                New Data Source
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step
                                editable
                                :complete="e1 > 2"
                                step="2"
                            >
                                Choose fields
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step step="3" editable>
                                Select a Geographic System
                            </v-stepper-step>
                        </v-stepper-header>

                        <v-stepper-items>
                            <v-stepper-content step="1">
                                <v-card>
                                    <v-card-text>
                                        <v-row>
                                            <v-col cols="12">
                                                <v-text-field
                                                    v-model="info.name"
                                                    label="Name"
                                                ></v-text-field>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-textarea
                                                    v-model="info.desc"
                                                    name="input-7-1"
                                                    label="Description"
                                                ></v-textarea>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-file-input
                                                    v-model="fArchivo"
                                                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .shp"
                                                    id="archivoExcel"
                                                    label="Upload data source"
                                                    outlined
                                                    dense
                                                    @change="subirExcel()"
                                                ></v-file-input>
                                                <v-alert
                                                    v-if="!iArchivo"
                                                    dense
                                                    outlined
                                                    type="error"
                                                >
                                                    Only XLLSX, XLS, CSV, SHP
                                                    files accepted
                                                </v-alert>
                                            </v-col>

                                            <v-combobox
                                                label="Select Sheet"
                                                v-model="selectHoja"
                                                :items="hojas"
                                                item-text="name"
                                                item-value="name"
                                            ></v-combobox>
                                        </v-row>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-btn color="primary" @click="e1 = 2">
                                            Continue
                                        </v-btn>

                                        <v-btn text>
                                            Cancel
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-stepper-content>

                            <v-stepper-content step="2">
                                <v-card>
                                    <v-card-text>
                                        <v-row>
                                            <v-col cols="12">
                                                <v-combobox
                                                    :items="items"
                                                    label="Node ID"
                                                    v-model="nodes.id"
                                                ></v-combobox>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-combobox
                                                    :items="items"
                                                    label="Node Name"
                                                    v-model="nodes.name"
                                                ></v-combobox>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-combobox
                                                    :items="items"
                                                    label="Node X"
                                                    v-model="nodes.x"
                                                ></v-combobox>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-combobox
                                                    :items="items"
                                                    label="Node Y"
                                                    v-model="nodes.y"
                                                ></v-combobox>
                                            </v-col>
                                        </v-row>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-btn color="primary" @click="e1 = 3">
                                            Continue
                                        </v-btn>

                                        <v-btn text>
                                            Cancel
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-stepper-content>

                            <v-stepper-content step="3">
                                <v-card
                                    class="mb-12"
                                    color="grey lighten-1"
                                    height="200px"
                                ></v-card>

                                <v-btn color="primary" @click="createp()">
                                    Create Project
                                </v-btn>

                                <v-btn text>
                                    Cancel
                                </v-btn>
                            </v-stepper-content>
                        </v-stepper-items>
                    </v-stepper>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="3"></v-col>
    </v-row>
</template>
<script>
import readXlsFile from "read-excel-file";
import download from "downloadjs";



    export default {
        name: "",
        props: [],
        components:{

        },
        data: () => ({
            e1: 1,
            items: [],
            hojas: [],
            selectHoja: null,
            nodes: {
              id: null,
              name: null,
              x: null,
              y: null
            },
            info:{
              name: "",
              desc: ""
            },
            datos: [],
            fArchivo: [],
            iArchivo:true,
        }),
        mounted(){

        },
        created(){

        },
        beforeMount(){

        },
        watch:{
          selectHoja(){
            console.log(this.selectHoja);
            readXlsFile(this.fArchivo, { sheet: this.selectHoja.name }).then((data) => {
              this.items = data[0];
              this.datos = data.slice(1);
              console.log(this.items);
            });
          }
        },
        computed:{

        },
        methods:{
          subirExcel(){
            console.log(this.fArchivo);
            if(this.fArchivo.type == 'text/csv' || this.fArchivo.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || this.fArchivo.type == 'application/vnd.ms-excel' || this.fArchivo.type == 'shp' ){
              this.iArchivo = true;


              const file = this.fArchivo;

            readXlsFile(file, { getSheets: true }).then((sheets) => {
                this.hojas = sheets;
                console.log(this.hojas);
            });

              let InstFormData = new FormData();
              InstFormData.append('archivo' , this.fArchivo);

              axios
        .post("/projects/check", InstFormData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((res) => {
          console.log(res.data);
        })
        .catch((error) => {});

            } else {
              this.iArchivo = false;
            }

            /*const input = document.getElementById("archivoExcel");
            const file = input.files[0];
            readXlsFile(file, { getSheet: true }).then((data) => {
                this.hojas = data;
            });
            readXlsFile(file).then((data) => {
                this.items = data[0];
                this.datos = data;
            })*/
            /*readSheetNames(input).then((sheetNames) => {
                this.items = sheetNames;
                console.log(this.items);
            })*/
            //readXlsFile(input.files[0]).then((rows)=>{     })

          },
          createp(){
            let InstFormData = new FormData();
            InstFormData.append('archivo' , this.fArchivo);


            axios
                .post("/projects/new", {archivo: this.fArchivo.name, nodos: this.nodes, titulos: this.items, datos: this.datos, info: this.info, hoja: this.selectHoja})
                .then((res) => {
                  const idProyecto = res.data.idProject;
                  const uuid = res.data.uuid;
                  InstFormData.append('id' , uuid);
                  axios
                    .post("/projects/archivoup", InstFormData, {
                      headers: {
                        "Content-Type": "multipart/form-data",
                      },
                    })
                    .then((res) => {
                      console.log(res.data);
                      this.$router.push('/verp/' + idProyecto);
                    })
                    .catch((error) => {});

                      
                })
                .catch((error) => {
           });

            //this.$router.push('/verp/1')
            //download(JSON.stringify(this.datos), "apidata.json", "text/plain");
          }
        }
    }
</script>
