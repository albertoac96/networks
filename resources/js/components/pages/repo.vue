<template>
    <v-app>
        <v-card>

            <v-card>
                <vue-excel-editor
                v-model="dessertsData"
                width="100%"
                height="400px"
                readonly
                disable-panel-filter
                disable-panel-setting
                ref="grid"
                filter-row
            >
           
            <vue-excel-column
                v-for="(column, index) in headersData"
                :key="index"
                :field="column.text"
                :label="column.value"
                type="string"
                readonly
                
                />
            </vue-excel-editor>
                </v-card>



            <v-toolbar flat color="primary" dark class="mt-4">
                <v-toolbar-title>RNG_Repository</v-toolbar-title>
            </v-toolbar>

         


            <v-tabs vertical v-model="selectedTab">
                <v-tab
                    v-for="(tab, index) in datasets"
                    :key="index"
                    @change="loadTabData(tab.idProject)"
                >
                    {{ tab.cName }}
                </v-tab>

               
                <v-tab-item v-for="(tab, index) in datasets" :key="index">
                    <v-card-text>
                        <p class="font-weight-medium" v-html="tab.cDescription"></p>
                        <v-data-table
                            :headers="headers"
                            :items="tab.grafos"
                            :loading="loading"
                            :items-per-page="5"
                            class="elevation-1"
                            fixed-header
                            height="300px"
                        >
                            <template v-slot:item.name="{ item }">
                                <span>
                                    <!-- Personaliza el nombre según el valor de nettype -->
                                    {{ formatName(item) }}
                                </span>
                            </template>

                            <template v-slot:item.beta="{ item }">
                                <span>
                                    {{ item.cContenido.nBeta }}
                                </span>
                            </template>

                            <template v-slot:item.download="{ item }">
                                <v-row>
                                    <v-col
                                        v-for="format in downloadFormats"
                                        :key="format.type"
                                    >
                                        <v-tooltip bottom>
                                            <template
                                                v-slot:activator="{ on, attrs }"
                                            >
                                                <v-img
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    max-width="25"
                                                    :src="format.icon"
                                                    @click="
                                                        download(
                                                            format.type,
                                                            item
                                                        )
                                                    "
                                                    class="hover-image mr-1"
                                                ></v-img>
                                            </template>
                                            <span>{{ format.label }}</span>
                                        </v-tooltip>

                                       


                                    </v-col>
                                    <v-col>
                                    <v-tooltip bottom>
                                            <template
                                                v-slot:activator="{ on, attrs }"
                                            >
                                                <v-img
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    max-width="25"
                                                    src="/images/vermapa.png"
                                                    @click="
                                                        verMapa(
                                                            'json',
                                                            item
                                                        )
                                                    "
                                                    class="hover-image mr-1"
                                                ></v-img>
                                            </template>
                                            <span>ver Mapa</span>
                                        </v-tooltip>

                                    </v-col>
                                </v-row>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-tab-item>
            </v-tabs>
        </v-card>



        
    <v-dialog
      v-model="dialog"
      width="500"
    >
      
      <v-card>
        

        <v-card-text>

            mapa

            <l-map
            ref="map"
            :zoom="zoom"
            :max-zoom="maxZoom"
            :min-zoom="minZoom"
            :center="center"
        
        >

        <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>

        <l-geo-json
           :geojson="geojson"
           :options-style="styleFunction"
         ></l-geo-json>
    
    
    
    </l-map>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            text
            @click="dialog = false"
          >
            I accept
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>




    </v-app>
</template>

<script>
import Papa from "papaparse";
import jsonData from '/storage/data.json';
import 'leaflet/dist/leaflet.css';
import {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    LIcon,
    LControlScale,
    LImageOverlay,
    LTooltip,
    LPolygon,
    LControlLayers,
    LLayerGroup,
    LGeoJson,
    LCircleMarker,
    LPolyline,
    LControl
} from "vue2-leaflet";
import * as Leaflet from "leaflet";
import { CRS, latLng } from "leaflet";
import LControlFullscreen from "vue2-leaflet-fullscreen";
import "leaflet-easyprint";
import "proj4leaflet";
import proj4 from "proj4";
import leafletImage from 'leaflet-image';


export default {
    name: "diego",
    props: [],
    components: {
        LMap,
        LTileLayer,
        LMarker,
        LPopup,
        LIcon,
        LControlScale,
       
        LTooltip,
       
        LControlLayers,
        LLayerGroup,
       
        LGeoJson,
       
        LControl
    },
    data: () => ({
        dialog: false,
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution:
        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      
        zoom: 15,
            maxZoom: 22,
            minZoom: 4,
            center: [51.505, -0.159],
            geojson: [],
        headersData: [
        { text: 'Clave', value: 'Clave' },
        { text: 'Toponym', value: 'Toponym' },
        { text: 'PopSV', value: 'PopSV' },
        { text: 'PopJV', value: 'PopJV' },
        { text: 'VivSV', value: 'VivSV' },
        { text: 'VivJV', value: 'VivJV' },
        { text: 'Population', value: 'Population' },
        { text: 'Population Rank', value: 'PopulationRank' },
        { text: 'Pop-Ref', value: 'Pop-Ref' },
        { text: 'Revisado', value: 'Revisado' },
        { text: 'Homonym', value: 'Homonym' },
        { text: 'ID Gibson', value: 'idGibson' },
        { text: 'ID GA', value: 'idGA' },
        { text: 'ID HB', value: 'idHB' },
        { text: 'ID SV', value: 'idSV' },
        { text: 'ID Hicks', value: 'idHicks' },
        { text: 'Modern Name', value: 'ModernName' },
        { text: 'Other Names', value: 'OtherNames' },
        { text: 'Municipality', value: 'Municipality' },
        { text: 'State', value: 'State' },
        { text: 'Latitude', value: 'Lat' },
        { text: 'Longitude', value: 'Long' },
        { text: 'Coordinate Source', value: 'CoordSource' },
        { text: 'Ethnicity', value: 'Ethnicity' },
        { text: 'Politics', value: 'Politics' },
        { text: 'Realm', value: 'Realm' },
        { text: 'Status', value: 'Estatus' },
        { text: 'Subject To', value: 'SubjectTo' },
        { text: 'Political Code', value: 'PoliticalCode' },
        { text: 'Political Rank', value: 'PoliticalRank' },
        { text: 'Territory', value: 'Territory' },
        { text: 'Number of Subject Towns', value: 'nSubjectTowns' },
        { text: 'Territorial Rank', value: 'TerritorialRank' },
        { text: 'Military', value: 'Military' },
        { text: 'Tribute', value: 'Tribute' },
        { text: 'Tributaries', value: 'Tributaries' },
        { text: 'Economy', value: 'Economy' },
        { text: 'Market', value: 'Market' },
        { text: 'Early Market Status', value: 'EarlyMartekStatus' },
        { text: 'Late Market Status', value: 'LateMarketStatus' },
        { text: 'Dock', value: 'Dock' },
        { text: 'Ceremonial Center', value: 'CeremonialCenter' },
        { text: 'Other', value: 'Other' },
        { text: 'Bibliography', value: 'Bibliography' },
        { text: 'Observations', value: 'Observations' },
        { text: 'Source Placename', value: 'SourcePlacename' },
        { text: 'Name Translation', value: 'NameTranslation' },
        { text: 'Translated By', value: 'TranslatedBy' },
        { text: 'Source Glyph', value: 'SourceGlyph' },
        { text: 'Change Name', value: 'ChangeName' }
        ],
        dessertsData: jsonData,
        selectedTab: 0,
        tabs: [],
        datasets: [],
        headers: [
            { text: "Net Type", value: "name" },
            { text: "Beta value", value: "beta" },
            { text: "Date Created", value: "created_at" },
            { text: "Download", value: "download", sortable: false }
        ],
        downloadFormats: [
            { type: "csv", label: "CSV", icon: "/images/cvs.png" },
            { type: "xlsx", label: "XLSX", icon: "/images/excel.png" },
            {
                type: "json",
                label: "GeoJSON",
                icon: "/images/geojson-file.svg"
            },
            { type: "shape", label: "ShapeFile", icon: "/images/shape.png" },
            { type: "zip", label: "ZIP", icon: "/images/zip.png" },
            
        ],
        loading: true
    }),
    mounted() {
       
        this.removeLoadingOverlay();
        setTimeout(() => {
    if (this.$refs.map) {
        this.map = this.$refs.map.mapObject;
    }
}, 10);
    },
    created() {
        // Simular la carga de datos desde un JSON
        const jsonData = {
            tabs: [
                {
                    title: "HB_64_Skeletons",
                    description: "Descripción para HB_64_Skeletons.",
                    items: [
                        {
                            name: "Item 1",
                            date_created: "2023-01-01",
                            size: "Small"
                        },
                        {
                            name: "Item 2",
                            date_created: "2023-01-02",
                            size: "Medium"
                        }
                    ]
                },
                {
                    title: "Gibson",
                    description: "Descripción para Gibson.",
                    items: [
                        {
                            name: "Item A",
                            date_created: "2023-01-01",
                            size: "Large"
                        }
                    ]
                }
            ]
        };
        this.tabs = jsonData.tabs;
    },
    beforeMount() {},
    watch: {},
    computed: {
        styleFunction() {
      const fillColor = this.fillColor; // important! need touch fillColor in computed for re-calculate when change fillColor
      return () => {
        return {
          weight: 2,
          color: "#ECEFF1",
          opacity: 1,
          fillColor: fillColor,
          fillOpacity: 1
        };
      };
    },
    },
    methods: {
        removeLoadingOverlay() {
            const overlay = document.getElementById("loading-overlay");
            if (overlay) {
                overlay.style.display = "none";
            }

            axios
                .get("/prueba")
                .then(res => {
                    console.log(res.data);
                    this.datasets = res.data;
                    this.loading = false;
                })
                .catch(error => {});
        },
        formatName(item) {
            switch (item.cContenido.netType) {
                case "bs":
                    return "Beta Skeleton";
                case "someValue2":
                    return `Custom Name 2 - ${item.cContenido.nBeta}`;
                case "someValue3":
                    return `Custom Name 3 - ${item.cContenido.nBeta}`;
                // Agrega más casos según sea necesario
                default:
                    return `Default Name - ${item.cContenido.nBeta}`;
            }
        },
        verMapa(tipo, item){
            this.dialog = true;
            this.map = this.$refs.map.mapObject;
            axios
                .post(
                    "/download",
                    { tipo: tipo, item: item }
                ) // Necesario para la descarga
                .then(res => {
                    console.log(res.data);
                    this.gejson = res.data;
                })
                .catch(error => {
                    console.error("Error al descargar el archivo:", error);
                });
        },
        download(tipo, item) {
            console.log(tipo);
            console.log(item);
            if(tipo == 'csv' || tipo == 'json'){
                axios
                .post(
                    "/download",
                    { tipo: tipo, item: item }
                ) // Necesario para la descarga
                .then(res => {
                 if (tipo == "csv") {
                        const jsonData = res.data; // Tu JSON
                        console.log(res);
                        // Convertir JSON a CSV
                        const csv = Papa.unparse(jsonData);

                        // Descargar el archivo CSV
                        this.downloadFile(csv, "nodes_table.csv");
                    } else {
                        var texto = JSON.stringify(res.data);
          const blob = new Blob([texto], { type: 'text/plain' });

          // Crea un objeto de enlace <a> para la descarga
          const enlaceDescarga = document.createElement('a');

          // Configura el enlace de descarga
          enlaceDescarga.href = window.URL.createObjectURL(blob);
          enlaceDescarga.download = "modelo.geojson";

          // Agrega el enlace de descarga al cuerpo del documento
          document.body.appendChild(enlaceDescarga);

          // Simula un clic en el enlace para iniciar la descarga
          enlaceDescarga.click();

          // Elimina el enlace después de la descarga
          document.body.removeChild(enlaceDescarga);
                    }
                })
                .catch(error => {
                    console.error("Error al descargar el archivo:", error);
                });
            } else {
                axios
                .post(
                    "/download",
                    { tipo: tipo, item: item },
                    { responseType: "blob" }
                ) // Necesario para la descarga
                .then(res => {
                    if (tipo == "xlsx") {
                        // Crear un objeto URL para el blob
                        const url = window.URL.createObjectURL(
                            new Blob([res.data])
                        );
                        const link = document.createElement("a");
                        link.href = url;

                        // Especificar el nombre del archivo para la descarga
                        const fileName = "archivo.xlsx"; // Asegúrate de que esto sea correcto
                        link.setAttribute("download", fileName);
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                    } else {
                        // Leer el nombre del archivo desde los headers
                    const contentDisposition =
                        res.headers["content-disposition"];
                    let filename = "default_name.zip"; // Un nombre por defecto
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(
                            /filename="?(.+)"?/
                        );
                        if (filenameMatch.length > 1) {
                            filename = filenameMatch[1];
                        }
                    }

                    // Procesar la descarga
                    const url = window.URL.createObjectURL(
                        new Blob([res.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", filename + ".zip");
                    document.body.appendChild(link);
                    link.click();
                    link.parentNode.removeChild(link);
                    }
                })
                .catch(error => {
                    console.error("Error al descargar el archivo:", error);
                });
            }
            
        },

        downloadFile(data, filename) {
            const blob = new Blob([data], { type: "text/csv;charset=utf-8;" });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = "hidden";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        loadTabData(projectId) {
            // Encuentra la posición del proyecto en datasets utilizando su id
            const projectIndex = this.datasets.findIndex(
                project => project.idProject === projectId
            );

            if (projectIndex !== -1) {
                const project = this.datasets[projectIndex];
                console.log(project);

                // Verifica si ya se han cargado los grafos
                if (!project.grafos || project.grafos.length === 0) {
                    this.loading = true;
                    axios
                        .get("/grafos/" + projectId)
                        .then(res => {
                            console.log(res.data);
                            // Asigna los grafos al array 'grafos' del proyecto
                            this.$set(
                                this.datasets[projectIndex],
                                "grafos",
                                res.data
                            );
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error("Error al cargar los grafos:", error);
                        });
                }
            } else {
                console.error("Proyecto no encontrado en datasets.");
            }
        },


       
    }
};
</script>

<style>
.hover-image {
    cursor: pointer; /* Cambia el cursor a manita al pasar el mouse */
}
</style>
