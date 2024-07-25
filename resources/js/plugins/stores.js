import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        drawer: false,
        grafos: [],
        menu:{
            drawer: true,
            mini: false
        },
        bandera:{
            newCapa: 0
        },
        selected:{
            tomo: null,
            relacion: null,
            parte: null,
            lugar: null,
            tipo: [],
            estatus: [],
            search: "",
            capa: [],
            elementos: []
        },
        verDetalle: [],
        overlay: false,
        iNewCarpeta: 0,
        reset: 0,
        resetRel: 0,
        
        rutaPublic: "/",
        selectGraph: [],
        selectGraphs: [],
        selectedItems: [],
        idGrafo: null,
        info: null,
        infoProyecto: null,
        singleTable: [],
        meanControl: null,
        stylePuntos:{
            radio: 11,
            color: "#FFFFFF",
            fillcolor: "#131313",
            weight: 3
        },
        tabLeft: 0,
        snackbar:{
            visible: false,
            text: "",
        },
        headers: {
            dataOriginal: [],
            singleTable: ["node_id", "node_name", "node_x", "node_y", "control_value", "relative_assymetry"],
            edges: [],
            adjacencyList: []
        },
        formatedData: {
            edges: null,
            adjacencyList: null,
            dataOriginal: null,
            singleTable: null,
            distanceMatrix: null
        }
    },
    getters: {

    },
    mutations: {

    }
})