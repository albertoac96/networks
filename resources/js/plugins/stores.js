import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        grafos: [],
        menu:{
            drawer: true,
            drawerR: true
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
        headers: [
            { text: 'id', value: 'id' }, 
            { text: 'Capa', value: 'capa' },
            { text: 'Placename', align: 'start', value: 'Placename', },
            { text: 'Lat', value: 'X' }, 
            { text: 'Long', value: 'Y' }, 
            { text: 'CveCarta', value: 'Cve_Carta' }
        ],
        rutaPublic: "/",
        selectGraph: [],
        selectGraphs: [],
        idGrafo: null,
        info: null,
        infoProyecto: null,
        singleTable: [],
        meanControl: null
    },
    getters: {

    },
    mutations: {

    }
})