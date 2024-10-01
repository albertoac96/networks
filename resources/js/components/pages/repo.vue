<template>
    <v-app>
        <v-card>
            <v-toolbar flat color="primary" dark>
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
                        <p class="font-weight-medium">{{ tab.cDescription }}</p>
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
                                </v-row>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-app>
</template>

<script>
import Papa from "papaparse";

export default {
    name: "diego",
    props: [],
    components: {},
    data: () => ({
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
            { type: "zip", label: "ZIP", icon: "/images/zip.png" }
        ],
        loading: true
    }),
    mounted() {
        this.removeLoadingOverlay();
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
    computed: {},
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
        }
    }
};
</script>

<style>
.hover-image {
    cursor: pointer; /* Cambia el cursor a manita al pasar el mouse */
}
</style>
