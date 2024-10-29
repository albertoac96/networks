<template>
   
<div>

    <div>
        <l-map
            ref="map"
            style="height:300px"
            :zoom="zoom"
            :max-zoom="maxZoom"
            :min-zoom="minZoom"
            :center="center"
            :options="leafletMapOptions"
        >
            <l-control-fullscreen
                position="bottomright"
                :options="{ title: { false: 'Go big!', true: 'Be regular' } }"
            />

            <l-control-scale
                position="bottomright"
                :imperial="true"
                :metric="true"
            ></l-control-scale>

           
        <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>

<l-geo-json
   :geojson="geojson"
   :options-style="styleFunction"
 ></l-geo-json>

           
        </l-map>



    </div>










    
    
</div>
</template>

<script>
import sidebar from './grafos.vue';
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

const swissCrs = new L.Proj.CRS(
    "EPSG:2056",
    "+proj=somerc +lat_0=46.95240555555556 +lon_0=7.439583333333333 +k_0=1 +x_0=2600000 +y_0=1200000 +ellps=bessel +towgs84=674.374,15.056,405.346,0,0,0,0 +units=m +no_defs",
    {
        resolutions: [
            4000,
            3750,
            3500,
            3250,
            3000,
            2750,
            2500,
            2250,
            2000,
            1750,
            1500,
            1250,
            1000,
            750,
            650,
            500,
            250,
            100,
            50,
            20,
            10,
            5,
            2.5,
            2,
            1.5,
            1,
            0.5
        ],
        origin: [2420000, 1350000]
    }
);

export default {
    components: {
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
        LControlFullscreen,
        LGeoJson,
        LCircleMarker,
        LPolyline,
        LControl,
        sidebar
    },
    props: ["lugares", "singleTable", "dialog", "json"],
    data() {
        return {
            url: "https://dh.inah.gob.mx/inegi50k/{z}/{x}/{y}.png",
            attribution:
                '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
            zoom: 8,
            maxZoom: 15,
            minZoom: 0,
            geojson: [],
            center: [20.0853643565, -98.76998],
            markerLatLng: [51.504, -0.159],
            options:{
                basemap: 0,
            },
            tooltip: {
                permanent: false,
                sticky: false,
                className: "leaflet-tooltip leaflet-tooltip-css",
                interactive: true
            },
            circle: {
                radio: 3
            },
            coordCentro: { x: 0, y: 0 },
            overlay: false,
            leafletMapOptions: {
                closePopupOnClick: false,
                doubleClickZoom: "center"
            },
            verCapas:false,
            geojson2: null,
            chkTooltip: false,
            geojsonLayer: null,
            markerObjects: null,
            crs: swissCrs,
            estilo: {
                weight: 2,
                color: "white",
                opacity: 1,
                fillColor: "white",
                fillOpacity: 0.5
            },
            mapa: {
                infoMapa: {
                    centro: { lat: "20.0853643565", long: "-98.76998" },
                    limites: {
                        visible: false,
                        nE: { lat: null, long: null },
                        nO: { lat: null, long: null },
                        sE: { lat: null, long: null },
                        sO: { lat: null, long: null }
                    },
                    zoom: { max: 20, min: 4, inicial: 10 },
                    mapasBase: {
                        "0": {
                            nombre: "ESRI",
                            atribution: "ESRI",
                            link:
                                "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}.png",
                            visible: false
                        },
                        "1": {
                            nombre: "INEGI",
                            atribution: "INEGI-Diego Jimenez",
                            link:
                                "https://dh.inah.gob.mx/inegi50k/{z}/{x}/{y}.png",
                            visible: true
                        }
                    }
                },
                capas: {}
            }
        };
    },
    mounted() {
        this.inicio();
        this.$nextTick(() => {
            this.map = this.$refs.map.mapObject; // work as expected
        });
    },
    async created() {},
    beforeMount() {},
    watch: {
        dialog(newValue) {
        if (newValue) {
            this.$nextTick(() => {
                // Forzar ajuste de tamaño del mapa cuando el diálogo se abra
                if (this.$refs.map) {
                    this.$refs.map.mapObject.invalidateSize();
                }
            });
        }
    },
    json(newValue){
        this.geojson = newValue;
    },
        chkTooltip(nuevo) {
            if (nuevo == false) {
                $(".leaflet-tooltip").css("display", "none");
            } else {
                $(".leaflet-tooltip").css("display", "block");
            }
        },
        "$store.state.selectGraph"(val, viejo) {
            if (val == []) {
                return;
            }

            /*console.log(viejo.length);
        if(viejo.length == undefined){
          console.log("borrando");
          layerGroup.clearLayers();
        }*/

            // Actualiza el GeoJSON
            this.geojsonLayer = this.$store.state.selectGraph.geo;

            /*var myLayer = L.geoJSON().addTo(this.map);
        myLayer.addData(this.$store.state.selectGraph.geo);

        var layerGroup = new L.LayerGroup();
        layerGroup.addTo(this.map);
        layerGroup.addLayer(myLayer);*/
        },
        singleTable(){
            if(this.singleTable){
                var centro = this.calculateCenter;
                this.zoom = this.mapa.infoMapa.zoom.inicial;
                console.log(centro);
                this.center = L.latLng(
                    parseFloat(centro.y),
                    parseFloat(centro.x)
                );
                this.map.setView(
                L.latLng(
                    centro.y,
                    centro.x
                ),
                this.mapa.infoMapa.zoom.inicial,
                { animate: true }
            );
                
                
            }
        }
    },
    computed: {
        calculateCenter() {
      const totalCoords = this.singleTable.length;
      console.log(this.singleTable);
      const sum = this.singleTable.reduce((acc, coord) => {
        acc.x += parseFloat(coord.NodeX);
        acc.y += parseFloat(coord.NodeY);
        return acc;
      }, { x: 0, y: 0 });

      return {
        x: sum.x / totalCoords,
        y: sum.y / totalCoords
      };
    },
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
        inicio() {
            console.log("ENTRO");
            this.maxZoom = this.mapa.infoMapa.zoom.max;
            this.minZoom = this.mapa.infoMapa.zoom.min;

            this.$nextTick(() => {
                // Forzar ajuste de tamaño del mapa cuando el diálogo se abra
                if (this.$refs.map) {
                    this.$refs.map.mapObject.invalidateSize();
                }
            });
        },
        getCoords(x, y) {
            return L.latLng(parseFloat(y), parseFloat(x));
        },
        fnReCenter() {
            this.map.setView(
                L.latLng(
                    this.mapa.infoMapa.centro.lat,
                    this.mapa.infoMapa.centro.long
                ),
                this.mapa.infoMapa.zoom.inicial,
                { animate: true }
            );
        },

        exportMap() {
    

      leafletImage(this.map, (err, canvas) => {
        if (err) {
          console.error(err);
          return;
        }
        const img = document.createElement('img');
        img.src = canvas.toDataURL();
        const link = document.createElement('a');
        link.href = img.src;
        link.download = 'mapa.jpg';
        link.click();
      });
    },

        fnTooltip() {
            let refToDisplay = "ref";

            $(".leaflet-tooltip").css("display", "block");

            console.log(this.mapa.capas);
        },

        fnDownloadJson(){
          var texto = JSON.stringify(this.$store.state.selectGraph.geo);
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
        },
        defineEstilo(item){
            console.log(item);
            if(item.properties && item.properties.stroke){
                return {
                    weight: item.properties['stroke-width'],
                    color: item.properties.stroke,
                    opacity: 1,
                    fillColor: item.properties.stroke,
                    fillOpacity: 0.5
                };
            } else {
                return {
                    weight: 2,
                    color: "white",
                    opacity: 1,
                    fillColor: "white",
                    fillOpacity: 0.5
                };
            }
        }
    }
};
</script>
