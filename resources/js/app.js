require('./bootstrap');

window.Vue = require('vue').default;

//const baseURL = 'https://dh.inah.gob.mx/adminrgs/';
//Vue.axios.defaults.baseURL = baseURL;


import vuetify from './plugins/vuetify' // path to vuetify export


import 'leaflet/dist/leaflet.css';
import "leaflet-geosearch/dist/geosearch.css";

Vue.config.productionTip = false;

import { Icon } from 'leaflet';

delete Icon.Default.prototype._getIconUrl;
Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});



import axios from 'axios';
import VueAxios from 'vue-axios';
Vue.use(VueAxios, axios);

// Configura Axios para incluir el token CSRF en las solicitudes
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Añade un interceptor de solicitud
axios.interceptors.request.use((config) => {
  const token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token.content;
  }
  return config;
});

// Añade un interceptor de respuesta para manejar errores CSRF
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response.message == 'CSRF token mismatch.') {
      // Token CSRF mismatch
      // Puedes redirigir al usuario al formulario de inicio de sesión aquí
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Permissions from './plugins/permisos.js';
Vue.mixin(Permissions);

import fnFechas from './plugins/fechas.js';
Vue.use(fnFechas);
import globales from './plugins/globales.js';
Vue.use(globales);

import Vue from 'vue';
import store from './plugins/stores.js'


/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { faUserSecret } from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(faUserSecret)

/* add font awesome icon component */
Vue.component('font-awesome-icon', FontAwesomeIcon)

import JsonExcel from "vue-json-excel";
 
Vue.component("downloadExcel", JsonExcel);

import VueExcelEditor from 'vue-excel-editor'

Vue.use(VueExcelEditor)


let routes = [
    
    //ELEMENTOS DEL MENU
    
    { path: '/', name: 'home', component: require('./components/pages/proyectos.vue').default },
    { path: '/newp', name: 'newp', component: require('./components/pages/newp.vue').default },
    { path: '/verp/:id', name: 'verp', component: require('./components/pages/viewp/verDataset.vue').default },
    
    

];

const router = new VueRouter({
    mode: 'history',
    //base: '/adminrgs/',
    routes, // short for `routes: routes`
});



Vue.component('app', require('./components/master/aplicacion.vue').default);

Vue.component('repo', require('./components/pages/repo.vue').default);



const app = new Vue({
    el: '#app',
    vuetify,
    router,
    Permissions,
    store
});
