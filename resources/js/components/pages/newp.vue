<template>

<v-dialog
      v-model="dialog"
      persistent
      max-width="900"
      overlay-color="black"
      :overlay-opacity="0.7" 
      :hide-overlay="false"
      :elevation="24"
    >
    <v-toolbar height="50px" flat color="#2B968A" >

        <img
              src="storage/assets/p3_newproject.png"
              alt="icon"
              style="width: 20px; height: 20px; rounded"
            />
               
               <span class="ml-2" style="color: white; font-size: 14px !important; font-family: 'Maven Pro', sans-serif; font-weight: 700;">New dataset</span>
           </v-toolbar>


            <v-card class="justify-center" style="height: 550px;">
               
                <v-card-text>
                    <v-stepper v-model="e1" flat color="purple" tile>
                        <v-stepper-header color="#2B968A">
                            <v-stepper-step
                                editable
                                :complete="e1 > 1"
                                step="1"
                                color="#43C2B4"
                            >
                                New Data Source
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step
                                editable
                                :complete="e1 > 2"
                                step="2"
                                color="#43C2B4"
                            >
                                Choose fields
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step step="3" editable color="#43C2B4">
                                Select a Geographic System
                            </v-stepper-step>
                        </v-stepper-header>

                        <v-stepper-items>
                            <v-stepper-content step="1">
                                <v-card>
                                    <v-card-text>
                                        <v-row dense class="d-flex align-center">

                                            <v-col cols="6">
                                                <v-file-input
                                                    v-model="fArchivo"
                                                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .shp"
                                                    id="archivoExcel"
                                                    label="Upload data source*"
                                                    outlined
                                                    dense
                                                    @change="subirExcel()"
                                                    hint="Only XLSX, XLS files accepted"
                                                    persistent-hint
                                                    color="#2B968A"
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
                                            <v-col cols="6">
                                            <v-combobox
                                            outlined
                                                    dense
                                                    color="#2B968A"
                                                    ref="hojas"  
                                                  :menu-props="{closeOnClick: false, closeOnContentClick: false, persistent: true}"
                                                label="Select Sheet*"
                                                v-model="selectHoja"
                                                :items="hojas"
                                                item-text="name"
                                                item-value="name"
                                            ></v-combobox>
                                            </v-col>


                                            <v-col cols="12">
                                                <v-text-field
                                                outlined
                                                    dense
                                                    v-model="info.name"
                                                    label="Name*"
                                                    color="#2B968A"
                                                ></v-text-field>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-textarea
                                                    outlined
                                                    v-model="info.desc"
                                                    name="input-7-1"
                                                    label="Description"
                                                    color="#2B968A"
                                                ></v-textarea>
                                            </v-col>

                                           
                                        </v-row>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn text @click="cancel()">
                                            Cancel
                                        </v-btn>
                                        <v-btn color="#43C2B4" @click="e1 = 2" :disabled="pasos.btn1">
                                            Continue
                                        </v-btn>

                                       
                                    </v-card-actions>
                                </v-card>
                            </v-stepper-content>

                            <v-stepper-content step="2">
                                <v-card>
                                    <v-card-text>
                                        <v-row dense>
                                            <v-col cols="12">
                                                <v-combobox
                                                outlined
                                                dense
                                                    :items="items"
                                                    label="Node ID"
                                                    v-model="nodes.id"
                                                    color="#2B968A"
                                                ></v-combobox>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-combobox
                                                outlined
                                                dense
                                                    :items="items"
                                                    label="Node Name"
                                                    v-model="nodes.name"
                                                    color="#2B968A"
                                                ></v-combobox>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-combobox
                                                outlined
                                                dense
                                                    :items="items"
                                                    label="Node X"
                                                    v-model="nodes.x"
                                                    @change="chkCoords()"
                                                    color="#2B968A"
                                                ></v-combobox>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-combobox
                                                outlined
                                                dense
                                                    :items="items"
                                                    label="Node Y"
                                                    v-model="nodes.y"
                                                    @change="chkCoords()"
                                                    color="#2B968A"
                                                ></v-combobox>
                                            </v-col>
                                        </v-row>

                                        <v-alert
                                            v-if="alert.visible"
                                            dense
                                            :outlined="alert.outlined"
                                            :type="alert.type"
                                            dismissible
                                        >
                                            {{ alert.text }}
                                        </v-alert>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn text @click="cancel()">
                                            Cancel
                                        </v-btn>
                                        <v-btn color="#43C2B4" @click="e1 = 3" :disabled="pasos.btn2">
                                            Continue
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-stepper-content>

                            <v-stepper-content step="3">
                                <v-card height="350px" tile flat>
                                <v-card-text>
                                   <p style="font-size: 1.2em">
                                    The coordinate system used is WGS 84 (EPSG:4326), the global standard for latitude and longitude. 
                                    It is compatible with Leaflet and widely used in web mapping. 
                                    We are working on incorporating additional coordinate systems in the future.
                                   </p>
                                   <p style="font-size: 1.2em">
                                   By creating the dataset, you agree to upload your information to the RNG Special Network Analysis server. 
                                   You can delete your data at any time by removing the dataset.
                                    </p>

                                </v-card-text>
                            
                            </v-card>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn text @click="cancel()">
                                            Cancel
                                        </v-btn>
                                        <v-btn color="#43C2B4" @click="createp()">
                                            Continue
                                        </v-btn>
                            </v-card-actions>
                               
                            </v-stepper-content>
                        </v-stepper-items>
                    </v-stepper>
                </v-card-text>
            </v-card>
     

    </v-dialog>

</template>
<script>
import readXlsFile from "read-excel-file";
import download from "downloadjs";

export default {
    name: "",
    props: ['dialog'],
    components: {},
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
        info: {
            name: "",
            desc: ""
        },
        datos: [],
        fArchivo: [],
        iArchivo: true,
        alert: {
            outlined: true,
            type: "error",
            text: "Hola",
            visible: false
        },
        pasos: {
            btn1: true,
            btn2: true,
            btn3: true
        },
        focus: {
            closeOnClick: false,
            closeOnContentClick: true
        }
    }),
    mounted() {},
    created() {},
    beforeMount() {},
    watch: {
        selectHoja() {
            console.log(this.selectHoja);
            readXlsFile(this.fArchivo, { sheet: this.selectHoja.name }).then(
                data => {
                    this.items = data[0];
                    this.datos = data.slice(1);
                    console.log("ITEMS");
                    console.log(this.items);
                    this.pasos.btn1 = false;
                }
            );
        },
        nodes: {
      handler(newValue) {
        // Llama al método para verificar si todas las propiedades son diferentes de null
        var allNodesNotNull = this.areAllPropertiesNotNull(newValue);
        console.log(allNodesNotNull);
        if(allNodesNotNull == true){
            this.pasos.btn2 = false;
        }
      },
      deep: true, // Necesario para observar cambios en las propiedades del objeto nodes
    },
    },
    computed: {},
    methods: {
        areAllPropertiesNotNull(obj) {
      // Verifica cada propiedad del objeto si tiene valor null
      for (const key in obj) {
        if (obj[key] === null) {
          return false;
        }
      }
      return true;
    },
        subirExcel() {
            console.log(this.fArchivo);
            if (
                this.fArchivo.type == "text/csv" ||
                this.fArchivo.type ==
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
                this.fArchivo.type == "application/vnd.ms-excel" ||
                this.fArchivo.type == "shp"
            ) {
                this.iArchivo = true;

                const file = this.fArchivo;
                this.info.name = file.name;

                readXlsFile(file, { getSheets: true }).then(sheets => {
                    this.hojas = sheets;
                    console.log(this.hojas);
                    this.$refs["hojas"].focus();
                    this.$refs["hojas"].activateMenu();
                    
                });

                let InstFormData = new FormData();
                InstFormData.append("archivo", this.fArchivo);

                axios
                    .post("/projects/check", InstFormData, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    })
                    .then(res => {
                        console.log(res.data);
                    })
                    .catch(error => {});
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
        chkCoords() {
            if ((this.nodes.x != null) & (this.nodes.y != null)) {
                console.log(this.nodes);
                const indexx = this.items.findIndex(
                    element => element === this.nodes.x
                );
                const indexy = this.items.findIndex(
                    element => element === this.nodes.y
                );
                console.log(indexx);
                console.log(indexy);
                console.log(this.selectHoja);
                readXlsFile(this.fArchivo, { sheet: this.selectHoja.name })
                    .then(rows => {
                        console.log(rows);
                        this.checkData(rows, indexx, indexy);
                    })
                    .catch(error => {
                        this.alert.outlined = true;
                        this.alert.type = "error";
                        this.alert.text = "Error reading sheet";
                        this.alert.visible = true;
                        console.error("Error reading sheet:", error);
                    });
            }
        },

        checkData(rows, ix, iy) {
            const latColIndex = iy; // Índice de la columna de Latitud (DD) Y1
            const longColIndex = ix; // Índice de la columna de Longitud (DD) X1
            const latitudes = new Set();
            const longitudes = new Set();
            let hasDuplicates = false;
            let hasEmptyFields = false;

            for (let i = 1; i < rows.length; i++) {
                // Comienza desde 1 para ignorar el encabezado
                const row = rows[i];
                const lat = row[latColIndex];
                const long = row[longColIndex];

                if (!lat || !long) {
                    hasEmptyFields = true;
                        this.alert.outlined = true;
                        this.alert.type = "error";
                        this.alert.text = `Empty field found in row ${i + 1}`;
                        this.alert.visible = true;
                    console.log(`Empty field found in row ${i + 1}`);
                }

                if (latitudes.has(lat) || longitudes.has(long)) {
                    hasDuplicates = true;
                        this.alert.outlined = true;
                        this.alert.type = "error";
                        this.alert.text = `Duplicate found in row ${i + 1}`;
                        this.alert.visible = true;
                    console.log(`Duplicate found in row ${i + 1}`);
                }

                latitudes.add(lat);
                longitudes.add(long);
            }

            if (!hasDuplicates && !hasEmptyFields) {
                    this.alert.outlined = true;
                        this.alert.type = "success";
                        this.alert.text = `No duplicates or empty fields found.`;
                        this.alert.visible = true;
                console.log("No duplicates or empty fields found.");
            } else {
                if (hasDuplicates)
                
                    console.log("There are duplicates in the data.");
                if (hasEmptyFields)
                
                    console.log("There are empty fields in the data.");
            }
        },
        createp() {
            let InstFormData = new FormData();
            InstFormData.append("archivo", this.fArchivo);

            axios
                .post("/projects/new", {
                    archivo: this.fArchivo.name,
                    nodos: this.nodes,
                    titulos: this.items,
                    datos: this.datos,
                    info: this.info,
                    hoja: this.selectHoja
                })
                .then(res => {
                    const idProyecto = res.data.idProject;
                    const uuid = res.data.uuid;
                    InstFormData.append("id", uuid);
                    axios
                        .post("/projects/archivoup", InstFormData, {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            }
                        })
                        .then(res => {
                            console.log(res.data);
                            this.$router.push("/verp/" + idProyecto);
                        })
                        .catch(error => {});
                })
                .catch(error => {});

            //this.$router.push('/verp/1')
            //download(JSON.stringify(this.datos), "apidata.json", "text/plain");
        },
        cancel(){
            this.$emit('cancel-dialog'); 
        }
    }
};
</script>

<style>

</style>
