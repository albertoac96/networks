<template>
    <div>
        <h4>Graphs Layers</h4>
        <v-subheader>Total: {{ $store.state.grafos.length }}</v-subheader>
        <v-btn @click="agrupar()">Agrupar grafos</v-btn>
        <v-btn @click="update()">Actualizar info</v-btn>

        <v-list subheader two-line flat>
            <v-list-item-group v-model="settings" multiple dense>
                <v-list-item v-for="(grafo, idGrafo) in $store.state.grafos" :key="idGrafo" three-line dense :class="{
                    'highlighted-item': highlighted === grafo.idGrafo
                }">
                    <v-list-item-action v-show="grafo.cContenido != null">
                        <v-checkbox :input-value="grafo.active" @change="toggleGrafo(grafo, $event)"
                            color="primary"></v-checkbox>
                    </v-list-item-action>

                    <v-list-item-content @click="verGrafo(grafo)">
                        <v-list-item-title>{{
                            nombre(grafo)
                            }}</v-list-item-title>
                        <v-subheader>{{ verInfo(grafo) }}</v-subheader>
                    </v-list-item-content>

                    <v-list-item-action>
                        <v-menu offset-y>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn icon v-bind="attrs" v-on="on">
                                    <v-icon color="grey lighten-1">mdi-dots-vertical</v-icon>
                                </v-btn>
                            </template>
                            <v-list flat>
                                <v-list-item>
                                    <v-list-item-title>Calculate Nodes
                                        Control</v-list-item-title>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Calculate Relative
                                        Assimetry</v-list-item-title>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>More information</v-list-item-title>
                                </v-list-item>
                                <v-list-item @click="DescargaInfo(grafo)">
                                    <v-list-item-title>Download</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-list-item-action>
                </v-list-item>
            </v-list-item-group>
        </v-list>
    </div>
</template>
<script>
export default {
    name: "verGrafos",
    props: [],
    components: {},
    data: () => ({
        grafos: [],
        settings: [],
        highlighted: null,
        selectedGrafos: []
    }),
    mounted() { },
    created() {
        const overlay = document.getElementById("loading-overlay");
        if (overlay) {
            overlay.style.display = "none";
        }
    },
    beforeMount() { },
    watch: {},
    computed: {},
    methods: {
        toggleGrafo(grafo, event) {
            console.log("ENTRE A TOGGLE");
            console.log(grafo);

            if (grafo.netType == "vd" || grafo.netType == "dt") {
                var contenido = grafo.cContenido;
            } else {
                var contenido = JSON.parse(grafo.cContenido);
            }

            console.log(contenido);
            console.log(this.$store.state.grafos);
            if (event === true) {
                this.$store.state.selectGraphs.push(contenido.geo);
                this.selectedGrafos.push(grafo);
            } else {
                const index = this.$store.state.selectGraphs.findIndex(
                    item => item.id == contenido.geo.id
                );
                console.log(contenido.geo.id);
                console.log(index);
                this.$store.state.selectGraphs.splice(index, 1);

                const index2 = this.selectedGrafos.findIndex(
                    item => item.idGrafo == grafo.idGrafo
                );
                this.selectedGrafos.splice(index2, 1);
            }
            console.log(this.$store.state.selectGraphs);
        },
        verInfo(grafo) {
            if (grafo.netType == "vd" || grafo.netType == "dt") {
                var contenido = grafo.cContenido;
            } else {
                var contenido = JSON.parse(grafo.cContenido);
            }

            var netType = this.verTipo(contenido.netType);

            var distFunction = this.verDistancia(contenido.distFunction);

            var LcResp =
                netType +
                " - (" +
                distFunction +
                ")" +
                "| Beta = " +
                contenido.nBeta +
                " ; Sigma = " +
                contenido.nSigma;
            return LcResp;
        },
        verDistancia(distFunction) {
            switch (distFunction) {
                case "hk":
                    distFunction = "Haversine in Kms";
                    break;
                case "hm":
                    distFunction = "Haversine in Milles";
                    break;
                case "e":
                    distFunction = "Euclidean";
                    break;
                default:
                    distFunction = "Unknown Dist Function";
                    break;
            }
            return distFunction;
        },
        verTipo(netType) {
            switch (netType) {
                case "vd":
                    netType = "Voronoi Diagram";
                    break;
                case "dt":
                    netType = "Delauney Triangulation";
                    break;
                case "gg":
                    netType = "Gabriel Graph";
                    break;
                case "bs":
                    netType = "Beta Skeleton";
                    break;
                case "rng":
                    netType = "Relative Neihbourhood Graph";
                    break;
                case "lng":
                    netType = "Limited Neihbourhood Graph";
                    break;
                default:
                    netType = "Unknown Network Type";
                    break;
            }
            return netType;
        },
        verGrafo(grafo) {
            this.highlighted = grafo.idGrafo;
            if (grafo.netType == "vd" || grafo.netType == "dt") {
                var contenido = grafo.cContenido;
            } else {
                var contenido = JSON.parse(grafo.cContenido);
            }

            this.$store.state.selectGraph = contenido;
            this.$store.state.idGrafo = grafo.idGrafo;
            this.$store.state.singleTable = contenido.nodes;

            this.$store.state.formatedData.distanceMatrix =
                contenido.distanceMatrix;

            console.log(contenido);

            this.crearListaAdiacencia();
            this.crearListaEdges();

            console.log(this.$store.state.headers);
            console.log(this.$store.state.formatedData);
        },
        nombre(grafo) {
            if (grafo.netType == "vd" || grafo.netType == "dt") {
                var contenido = grafo.cContenido;
            } else {
                var contenido = JSON.parse(grafo.cContenido);
            }

            // Fecha en formato ISO 8601
            let fechaISO = grafo.created_at;

            // Crear un objeto Date a partir de la cadena ISO
            let fecha = new Date(fechaISO);

            // Obtener el día, el mes y el año
            let dia = fecha.getUTCDate();
            let mes = fecha.getUTCMonth() + 1; // Los meses en JavaScript van de 0 a 11
            let anio = fecha.getUTCFullYear();

            // Formatear la fecha en el formato deseado
            let fechaFormateada = `${dia}-${mes}-${anio}`;

            var netType = this.verTipo(contenido.netType);

            var distFunction = this.verDistancia(contenido.distFunction);

            var beta = contenido.nBeta;

            var LcResp =
                fechaFormateada +
                "_" +
                grafo.idProyecto +
                "_" +
                grafo.idGrafo +
                "_" +
                contenido.netType +
                "_" +
                beta;

            return LcResp;
        },
        agrupar() {
            console.log(this.selectedGrafos);
            var tablaFinal = [];

            for (var i = 0; i < this.selectedGrafos.length; i++) {
                var grafo = this.selectedGrafos[i];
                var contenido = JSON.parse(grafo.cContenido);
                grafo.conteNew = contenido;
                var netType = contenido.netType;
                var beta = contenido.nBeta;
                var nameGrafo = grafo.idGrafo + "_" + beta + "_" + netType;

                var nodes = contenido.nodes;
                var grafoNombre = this.nombre(grafo);
                console.log(nodes);

                var control = "control_" + nameGrafo;
                var ra = "ra_" + nameGrafo;

                var controlValueSum = 0;
                var RelativeAssymetrySum = 0;

                for (var u = 0; u <= nodes.length; u++) {
                    if (u < nodes.length) {
                        if (!tablaFinal[u]) {
                            tablaFinal[u] = {
                                NodeID: nodes[u].NodeID,
                                "Node Name": nodes[u].NodeName,
                                X: nodes[u].NodeX,
                                Y: nodes[u].NodeY,
                                [control]: nodes[u].ControlValue,
                                [ra]: nodes[u].RelativeAssymetry
                            };
                        } else {
                            tablaFinal[u][control] = nodes[u].ControlValue;
                            tablaFinal[u][ra] = nodes[u].RelativeAssymetry;
                        }
                        controlValueSum += nodes[u].ControlValue;
                        RelativeAssymetrySum += nodes[u].RelativeAssymetry;
                    } else {
                        if (u === nodes.length) {
                            console.log("grafo-" + grafo.idGrafo);
                            tablaFinal[u] = {
                                NodeID: "Promedio",
                                "Node Name": "",
                                X: "",
                                Y: "",
                                [control]: controlValueSum / nodes.length,
                                [ra]: RelativeAssymetrySum / nodes.length
                            };
                        }
                        console.log(tablaFinal);
                    }
                }
            }

            console.log("HOLA");

            // Convertir tablaFinal a CSV y descargar
            this.downloadCSV(tablaFinal);
        },
        promedio(items, tipo) {
            console.log("entre a promedio " + tipo);
            console.log(items);
            let nSum = 0;
            for (var i = 0; i < items.length; i++) {
                nSum += items[i][tipo];
                //console.log(items[i][tipo]);
            }
            return nSum / items.length;
        },
        downloadCSV(data) {
            const replacer = (key, value) => (value === null ? "" : value); // replace null values with empty strings
            const header = Object.keys(data[0]);
            const csv = [
                header.join(","), // header row first
                ...data.map(row =>
                    header
                        .map(fieldName =>
                            JSON.stringify(row[fieldName], replacer)
                        )
                        .join(",")
                )
            ].join("\r\n");

            // Crear un blob de CSV y un enlace para descargarlo
            const blob = new Blob([csv], { type: "text/csv" });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "nodes_data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        },
        creaEncabezadosAdiacencia(maxRelaciones) {
            var encabezados = ["Node"]; // El primer encabezado es el nodo en sí
            // Determinar el número máximo de relaciones
            for (
                let i = 0;
                i < this.$store.state.selectGraph.adjacencyList.length;
                i++
            ) {
                if (
                    this.$store.state.selectGraph.adjacencyList[i].length >
                    maxRelaciones
                ) {
                    maxRelaciones = this.$store.state.selectGraph.adjacencyList[
                        i
                    ].length;
                }
            }

            // Crear encabezados para las relaciones
            for (let i = 1; i <= maxRelaciones; i++) {
                encabezados.push("Relation-" + i);
            }

            var LcResp = {
                encabezados: encabezados,
                maxRelaciones: maxRelaciones
            };

            return LcResp;
        },
        crearListaAdiacencia() {
            var maxRelaciones = 0;
            var funcionEnc = this.creaEncabezadosAdiacencia(maxRelaciones);
            var encabezados = funcionEnc["encabezados"];
            maxRelaciones = funcionEnc["maxRelaciones"];
            // Iniciar las variables necesarias
            var jsondata = [];

            var index = null;

            console.log("ADIACENCIA");
            //console.log(this.$store.state.selectGraph.adjacencyList);
            console.log("SINGLE TABLE");
            //console.log(this.$store.state.singleTable);

            // Recorrer la lista de adyacencia
            for (
                var i = 0;
                this.$store.state.selectGraph.adjacencyList.length > i;
                i++
            ) {
                //console.log(this.$store.state.singleTable[i]);

                // Crear el objeto con el nodo y sus relaciones
                var elemento = {
                    Node:
                        this.$store.state.singleTable[i].NodeID +
                        "-" +
                        this.$store.state.singleTable[i].NodeName
                };

                // Añadir las relaciones a las columnas correspondientes
                for (var u = 0; u < maxRelaciones; u++) {
                    if (
                        u <
                        this.$store.state.selectGraph.adjacencyList[i].length
                    ) {
                        if (this.$store.state.selectGraph.netType == "vd") {
                            index = this.$store.state.selectGraph.adjacencyList[
                                i
                            ][u];
                        } else {
                            index = this.$store.state.selectGraph.adjacencyList[
                                i
                            ][u][0];
                        }

                        if (index == -1) {
                            // console.log("NO EXISTE");
                            elemento["Relation-" + (u + 1)] = "NO EXISTE";
                        } else {
                            // console.log(this.$store.state.singleTable[index]);
                            elemento["Relation-" + (u + 1)] =
                                this.$store.state.singleTable[index].NodeID +
                                "-" +
                                this.$store.state.singleTable[index].NodeName;
                        }
                    } else {
                        elemento["Relation-" + (u + 1)] = ""; // Añadir una cadena vacía si no hay más relaciones
                    }
                }

                //console.log(elemento);
                jsondata.push(elemento);
            }
            this.$store.state.headers.adjacencyList = null;
            this.$store.state.headers.adjacencyList = encabezados;
            this.$store.state.formatedData.adjacencyList = jsondata;

            //console.log(this.encabezados);
            //console.log(this.jsondata);
        },

        crearListaEdges() {
            //  console.log(this.$store.state.selectGraph.EdgesList);
            var data = [];
            for (
                var i = 0;
                i < this.$store.state.selectGraph.EdgesList.length;
                i++
            ) {
                var index_node = this.$store.state.selectGraph.EdgesList[i][0];
                var index_edge = this.$store.state.selectGraph.EdgesList[i][1];
                var item = {
                    node_id: this.$store.state.singleTable[index_node].NodeID,
                    node_name: this.$store.state.singleTable[index_node]
                        .NodeName,
                    edge_id: this.$store.state.singleTable[index_edge].NodeID,
                    edge_name: this.$store.state.singleTable[index_edge]
                        .NodeName
                };
                data.push(item);
            }
            this.$store.state.headers.edges = [
                "node_id",
                "node_name",
                "edge_id",
                "edge_name"
            ];
            this.$store.state.formatedData.edges = data;
        },

        update() {
            for (var i = 0; i < this.$store.state.grafos.length; i++) {
                var grafo = this.$store.state.grafos[i];
                var sTable = JSON.parse(grafo.cContenido);
                sTable = sTable.nodes;
                this.$store.state.formatedData.singleTable = Object.values(
                    sTable
                ).map(item => {
                    return {
                        node_id: item.NodeID,
                        node_name: item.NodeName,
                        node_x: item.NodeX,
                        node_y: item.NodeY,
                        rcontrol_value: item.ControlValue, // Si control_value es el valor 0 del objeto
                        relative_assymetry: item.RelativeAssymetry
                    };
                });
                this.verGrafo(grafo);
                var data = {
                    grafo: grafo,
                    headers: this.$store.state.headers,
                    formatedData: this.$store.state.formatedData
                };
                console.log(data);
                axios
                    .post("/updategrafos", data)
                    .then((res) => {
                    console.log(res.data);
                
                })
                .catch((error) => {
                });
            }
        },
        DescargaInfo(grafo) {
            console.log(grafo);
            var sTable = JSON.parse(grafo.cContenido);
            sTable = sTable.nodes;
            this.$store.state.formatedData.singleTable = Object.values(
                sTable
            ).map(item => {
                return {
                    node_id: item.NodeID,
                    node_name: item.NodeName,
                    node_x: item.NodeX,
                    node_y: item.NodeY,
                    rcontrol_value: item.ControlValue, // Si control_value es el valor 0 del objeto
                    relative_assymetry: item.RelativeAssymetry
                };
            });
            this.verGrafo(grafo);
            var data = {
                grafo: grafo,
                headers: this.$store.state.headers,
                formatedData: this.$store.state.formatedData
            };

            axios
                .post("/projects/download", data, { responseType: "blob" })
                .then(res => {
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
                })
                .catch(error => { });
        }
    }
};
</script>

<style>
.highlighted-item {
    background-color: #b3e5fc;
    /* Color de fondo para el elemento destacado */
}
</style>
