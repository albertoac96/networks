<template>
    <v-row flat dense>
       
            <!--<h5>Project Info</h5>
            <v-row>
        <v-col
          cols="12"
          
        >
          <v-text-field
            v-model="info.cName"
            label="Name"
            @change="cambio(info.cName, 'cName')"
          ></v-text-field>
        </v-col>
        <v-col
        cols="12"
      
      >
        <v-textarea
        v-model="info.cDescription"
          label="Description"
          @change="cambio(info.cDescription, 'cDescription')"
        ></v-textarea>
      </v-col>
        </v-row>
            
            <b>Created at:</b> {{ this.cFechaYMDlarga(info.created_at) }} <br>
            <b>Updated at:</b> {{ this.cFechaYMDlarga(info.updated_at) }} <br>
            <b>Original name: </b> {{ info.cDocName }} <br>
            <b>Sheet: </b> {{ info.cSheet }} <br>
        </p>

            <v-col cols="12">
                <v-btn color="error" @click="fnNodeControl()"
                    >Calculate Nodes Control</v-btn
                >
            </v-col>-->

            <v-toolbar height="30px" flat color="#2B968A" class="d-flex justify-center align-center" >
                <span class="ml-2" style="color: white; font-family: 'Maven Pro', sans-serif; font-weight: 700;">Compute Graph</span>
            </v-toolbar>

           
            <v-row class="m-2">
            <v-col cols="12" dense>
                <h5>Network Type</h5>

                <v-radio-group v-model="cg.netType" row color="#2B968A">
                    <v-radio
                    color="#2B968A"
                        v-for="item in models"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    ></v-radio>
                </v-radio-group>

                <v-row>
                    <v-flex xs1></v-flex>
                    <v-flex xs4>
                        <v-text-field
                            :disabled="disBeta"
                            label="Beta"
                            type="number"
                            step="any"
                            min="0"
                            ref="input"
                            color="#2B968A"
                            :rules="[numberRule]"
                            v-model="cg.beta"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs2></v-flex>

                    <v-flex xs4>
                        <v-text-field
                            :disabled="disSigma"
                            label="Sigma"
                            type="number"
                            step="any"
                            min="0"
                            ref="input"
                            color="#2B968A"
                            :rules="[numberRule]"
                            v-model="cg.sigma"
                        ></v-text-field>
                    </v-flex>
                </v-row>
                <v-flex xs1></v-flex>
            </v-col>

            <v-col cols="12" dense>
                <h5>Distance function</h5>
                <v-radio-group v-model="cg.distFuntion" row color="#2B968A">
                    <v-radio
                    color="#2B968A"
                        v-for="item in distance"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    ></v-radio>
                </v-radio-group>
            </v-col>

            <v-col cols="12" dense>
                <h5>Calculate</h5>
                <v-radio-group row color="#2B968A" multiple v-model="cg.control">
                    <v-radio
                    color="#2B968A"
                       :value="1"
                       
                        label="Control value"
                        
                    ></v-radio>
                    <v-radio
                    color="#2B968A"
                    :value="2"
                   
                        label="Relative assymetry"
                        
                    ></v-radio>
                </v-radio-group>
            </v-col>
        </v-row>
            <v-col cols="12">
                <v-btn color="error" block @click="fnProccess()">Compute</v-btn>
            </v-col>

     
      
    </v-row>
</template>

<script>
import { Delaunay } from "d3-delaunay";

export default {
    name: "",
    props: ["info", "singleTable"],
    components: {},
    data: () => ({
        tab: null,
        models: [
            { name: "Voronoi Diagram", id: "vd" },
            { name: "Delauney Triangulation", id: "dt" },
            { name: "Gabriel Graph", id: "gg" },
            { name: "Beta Skeleton", id: "bs" },
            { name: "Relative Neihbourhood Graph", id: "rng" },
            { name: "Limited Neihbourhood Graph", id: "lng" }
        ],
        distance: [
           
            { name: "Euclidean", id: "e" }
        ],
        cg: {
            beta: "1",
            sigma: "0",
            info: null,
            distFuntion: "e",
            netType: "bs",
            singleTable: [],
            control: [1, 2]
        },
        selectModel: null,
        selectDis: "hk",
        disSigma: true,
        disBeta: false,
        disHV: false,
        numberRule: val => {
            if (val < 0) return "Please enter a positive number";
            return true;
        },
        polygons: [],
        control: null
    }),
    mounted() {
        console.log(this.singleTable);
    },
    created() {},
    beforeMount() {},
    watch: {
        "cg.netType"(val) {
            switch (val) {
                case "vd":
                    this.disSigma = true;
                    this.disBeta = true;
                    this.cg.distFuntion = "e";
                    this.cg.beta = 1;
                    this.cg.sigma = 0;
                    break;
                case "dt":
                    this.disSigma = true;
                    this.disBeta = true;
                    this.cg.distFuntion = "e";
                    this.cg.beta = 1;
                    this.cg.sigma = 0;
                    break;
                case "gg":
                    this.disSigma = true;
                    this.disBeta = true;
                    this.cg.beta = 1;
                    this.cg.sigma = 0;
                    break;
                case "bs":
                    this.disSigma = true;
                    this.disBeta = false;
                    this.cg.beta = 1;
                    this.cg.sigma = 0;
                    break;
                case "rng":
                    this.disSigma = true;
                    this.disBeta = true;
                    this.cg.beta = 2;
                    this.cg.sigma = 0;
                    break;
                case "lng":
                    this.disSigma = false;
                    this.disBeta = false;
                    this.cg.beta = 1;
                    this.cg.sigma = 0;
                    break;
            }
        },
        "cg.singleTable"(val) {
            //this.cg.singleTable = val;
        }
    },
    computed: {},
    methods: {
        cambio(valor, campo){
            axios
                .post("/projects/update", { valor: valor, campo: campo, id: this.info.idProject })
                .then(res => {
                    console.log("OK");
                    this.$store.state.snackbar.text = "The graph has been updated.";
                    this.$store.state.snackbar.visible = true;

                })
                .catch(error => {});
        },
        fnProccess() {
            this.$store.state.overlay = true;
            this.cg.info = this.info;
            this.cg.singleTable = this.singleTable;
            console.log(this.cg);
            if (this.cg.netType == "vd") {
                this.generateVoronoi();
                return;
            } else if(this.cg.netType == "dt"){
                this.generaDelaunay();
                return;
            }
            axios
                .post("/projects/compute", this.cg)
                .then(res => {
                    console.log("COMPUTE");
                    var contenido = JSON.parse(res.data.grafo[0].cContenido);
                    this.$store.state.selectGraph = contenido;
                    this.$store.state.idGrafo = res.data.grafo[0].idGrafo;
                    this.$store.state.singleTable = contenido.nodes;
                    this.$store.state.grafos.unshift(res.data.grafo[0]);
                    var total = this.$store.state.grafos.length;
                    var contenidoNuevo = JSON.parse(
                        this.$store.state.grafos[total - 1].cContenido
                    );
                    this.$store.state.grafos[0].active = true;
                    this.$store.state.selectGraphs.unshift(contenidoNuevo.geo);
                    console.log(contenido);
                    console.log(res.data);
                    console.log(this.$store.state.grafos);
                    this.$store.state.tabLeft = 0;

                    this.$store.state.snackbar.text = "The graph has been processed successfully.";
                    this.$store.state.snackbar.visible = true;
                    this.$store.state.overlay = false;

                })
                .catch(error => {});
        },
        fnNodeControl() {
            console.log(this.$store.state.selectGraph);
            axios
                .post("/projects/controlnode", {
                    info: this.$store.state.selectGraph.infoProyecto,
                    idGrafo: this.$store.state.idGrafo
                })
                .then(res => {
                    for (
                        var i = 0;
                        i < res.data.controlValuesArray.length;
                        i++
                    ) {
                        this.$store.state.singleTable[i].ControlValue =
                            res.data.controlValuesArray[i];
                        this.$store.state.singleTable[i].RelativeAssymetry =
                            res.data.relativeAssymmetry[i];
                        this.$store.state.meanControl = res.data.meanControl;
                    }
                })
                .catch(error => {});
        },
        generateVoronoi() {
            console.log(this.cg);
            const data = this.cg.singleTable;
            // Transforma los datos para obtener un array de puntos [lng, lat]
            const points = data.map(point => [point.NodeX, point.NodeY]);
            console.log(points);

            const bbox = this.calculateBoundingBox(points);


            const delaunay = Delaunay.from(points);
            const voronoi = delaunay.voronoi(bbox);
            const polygons = Array.from(voronoi.cellPolygons());

            this.polygons = polygons.map(
                polygon => polygon.map(point => [point[1], point[0]]) // Leaflet necesita [lat, lng]
            );

            console.log(polygons);

            var final = {
                idGrafo: 4500,
                netType: "vd",
                cContenido: {
                    EdgesList: [],
                    PbDrawCurrentEdge: null,
                    adjacencyList: this.calculateAdjacencyList(delaunay, points),
                    distFunction: "e",
                    geo: this.creaGeoJSON(polygons),
                    netType: "vd",
                    nBeta: 0,
                    nSigma: 0
                },
                active: true
            };
            JSON.stringify(final.cContenido);
            this.$store.state.grafos.push(final);
            console.log(this.$store.state.grafos);
            var total = this.$store.state.grafos.length;
            this.$store.state.selectGraphs.push(final.cContenido.geo);

            

            console.log(this.$store.state.selectGraphs);
            this.$store.state.tabLeft = 0;
        },
        creaGeoJSON(polygons) {
            console.log("creaGeoJSON");
            var geojson = {
                crs: {
                    properties: {
                        name: ""
                    },
                    type: ""
                },
                features: this.creaPoligonosGeo(polygons, "Polygon"),
                id: 4500,
                netType: "vd",
                name: "grafo",
                type: "FeatureCollection"
            };
            return geojson;
        },
        creaPoligonosGeo(polygons, type) {
            console.log("creaPoligonosGeo");
            console.log(polygons.length);
            var LcResp = [];
            for (var i = 0; polygons.length > i; i++) {
                console.log("ENTRE AL FOR");
                var geoPolygon = {
                    type: "Feature",
                    geometry: {
                        type: type,
                        coordinates: [polygons[i]]
                    },
                    properties: {
                        name: "linea-" + polygons[i]
                    }
                };
                LcResp.push(geoPolygon);
            }
            return LcResp;
        },
        calculateBoundingBox(points) {
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;

            points.forEach(([x, y]) => {
                if (x < minX) minX = x;
                if (y < minY) minY = y;
                if (x > maxX) maxX = x;
                if (y > maxY) maxY = y;
            });

            // Añadir un pequeño margen
            const margin = 0.01;
            minX -= margin;
            minY -= margin;
            maxX += margin;
            maxY += margin;

            return [minX, minY, maxX, maxY];
        },
        generaDelaunay(){
            const data = this.cg.singleTable;
            // Transforma los datos para obtener un array de puntos [lng, lat]
            const points = data.map(point => [point.NodeY, point.NodeX]);
            const delaunay = Delaunay.from(points);
            var delaunayEdges = [];
            for (let i = 0; i < delaunay.triangles.length; i += 3) {
                const triangle = [
                delaunay.triangles[i],
                delaunay.triangles[i + 1],
                delaunay.triangles[i + 2]
                ];
                for (let j = 0; j < 3; j++) {
                    const start = points[triangle[j]];
                    const end = points[triangle[(j + 1) % 3]];
                    delaunayEdges.push([
                        [start[1], start[0]], // Leaflet necesita [lat, lng]
                        [end[1], end[0]]
                    ]);
                }
            }
            console.log(delaunayEdges);

            var features = [];
            for (var i = 0; delaunayEdges.length > i; i++) {
                console.log("ENTRE AL FOR");
                console.log(delaunayEdges[i]);
              
                var geoPolygon = {
                    type: "Feature",
                    geometry: {
                        type: "LineString",
                        coordinates: delaunayEdges[i]
                    },
                    properties: {
                        name: "linea-"
                    }
                };
                features.push(geoPolygon);
            }
            console.log(features);

            var geojson = {
                crs: {
                    properties: {
                        name: ""
                    },
                    type: ""
                },
                features: features,
                id: 4600,
                netType: "dt",
                name: "grafo",
                type: "FeatureCollection"
            };


            var final = {
                idGrafo: 4600,
                netType: "dt",
                cContenido: {
                    EdgesList: [],
                    PbDrawCurrentEdge: false,
                    adjacencyList: [],
                    distFunction: "e",
                    geo: geojson,
                    netType: "dt",
                    nBeta: 0,
                    nSigma: 0
                },
                active: true
            };

            this.$store.state.grafos.push(final);
            var total = this.$store.state.grafos.length;
            this.$store.state.selectGraphs.push(final.cContenido.geo);

            console.log(this.$store.state.selectGraphs);
            this.$store.state.tabLeft = 0;


        },
        calculateAdjacencyList(delaunay, points) {
            console.log("LISTA ADIACEN");
            const neighbors = [];
                for (let i = 0; i < points.length; i++) {
                    neighbors.push([...delaunay.neighbors(i)]);
                }
            var LcResp = neighbors;
            console.log(LcResp);
            return LcResp;
        },
        calculateEdges(voronoi) {
            const edges = [];
            for (let e = 0; e < voronoi.edges.length; e++) {
                const edge = voronoi.edge(e);
                if (edge) {
                const [start, end] = edge;
                edges.push([
                    [start[1], start[0]], // Leaflet necesita [lat, lng]
                    [end[1], end[0]]
                ]);
                }
            }
            var LcResp = edges;
            console.log(edges);
        }
    }
};
</script>
