<template>
   
<div>

   <!-- <v-toolbar dense>
        
   
            <v-btn-toggle
        v-model="options.basemap"
        mandatory
        dense
        outlined
      >
        <v-btn outlined>
          <v-icon>mdi-format-align-left</v-icon>
        </v-btn>

        <v-btn outlined>
          <v-icon>mdi-format-align-center</v-icon>
        </v-btn>

        <v-btn outlined>
          <v-icon>mdi-format-align-right</v-icon>
        </v-btn>

      </v-btn-toggle>

      <v-btn dense elevation="0" outlined>
          <v-icon>mdi-format-align-right</v-icon>
        </v-btn>
        <v-btn dense elevation="0" outlined>
          <v-icon>mdi-format-align-right</v-icon>
        </v-btn>

</v-toolbar>-->
  
    <div>
        <l-map
            ref="map"
            style="height:85vh"
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

            <l-tile-layer
                v-for="tileProvider in mapa.infoMapa.mapasBase"
                :key="tileProvider.nombre"
                :name="tileProvider.nombre"
                :visible="tileProvider.visible"
                :url="tileProvider.link"
                :attribution="tileProvider.atribution"
                layer-type="base"
            />

            <l-control-layers
                position="topright"
                :collapsed="false"
            ></l-control-layers>

            <l-control position="bottomright">
                <v-card>
                    <div>
                        <v-chip
                            small
                            color="primary"
                            outlined
                            @click="fnReCenter()"
                        >
                            <v-icon small dark
                                >mdi-image-filter-center-focus</v-icon
                            >Restablecer vista
                        </v-chip>
                    </div>
                </v-card>
            </l-control>


           <!-- <l-control position="bottomleft" v-if="this.$store.state.selectGraph!=[]">
                <v-card>
                    <div>
                        <v-chip
                            small
                            color="error"
                            outlined
                            @click="fnDownloadJson()"
                        >
                            <v-icon small dark
                                >mdi-image-filter-center-focus</v-icon
                            >Descargar GeoJSON
                        </v-chip>
                    </div>
                </v-card>
            </l-control>-->

            <l-control position="topright">
                <v-card class="pa-2">
                    <v-chip small color="success" outlined>
                        <input
                            type="checkbox"
                            id="chkTool"
                            v-model="chkTooltip"
                        />
                        Mostrar etiquetas
                    </v-chip>
                </v-card>

                <button @click="exportMap">Exportar Mapa</button>

               <div> <v-menu offset-y>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="primary"
          dark
          v-bind="attrs"
          v-on="on"
        >
          Dropdown
        </v-btn>
      </template>
     <label>hola</label>
    </v-menu>
</div>

            </l-control>

            <l-circle-marker
                v-for="item in singleTable"
                :key="item.NodeID"
                :lat-lng="getCoords(item.NodeX, item.NodeY)"
                :radius="$store.state.stylePuntos.radio"
                :color="$store.state.stylePuntos.color"
                :fillColor="$store.state.stylePuntos.fillcolor"
                :fillOpacity="1"
                :weight="$store.state.stylePuntos.weight"
            >
                <l-tooltip :options="tooltip">{{ item.NodeID }}</l-tooltip>
                <l-popup :content="item.NodeName" />
            </l-circle-marker>

            <!--<l-geo-json :geojson="$store.state.selectGraph.geo"> </l-geo-json>-->

            <l-geo-json
      v-for="geoItem in $store.state.selectGraphs"
      :key="geoItem.id"
      :geojson="geoItem"
    ></l-geo-json>

           
        </l-map>
    </div>
    
    
</div>
</template>

<script>
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
        LControl
    },
    props: ["lugares", "singleTable"],
    data() {
        return {
            url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            attribution:
                '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
            zoom: 15,
            maxZoom: 22,
            minZoom: 0,
            center: [51.505, -0.159],
            markerLatLng: [51.504, -0.159],
            options:{
                basemap: 0,
            },
            tooltip: {
                permanent: true,
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
            geojson2: null,
            chkTooltip: true,
            geojsonLayer: null,
            markerObjects: null,
            crs: swissCrs,
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
                                "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}.png"
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
                console.log(centro);
                this.center = L.latLng(
                    centro.y,
                    centro.x
                );
                this.zoom = this.mapa.infoMapa.zoom.inicial;
            }
        }
    },
    computed: {
        calculateCenter() {
      const totalCoords = this.singleTable.length;
      console.log(this.singleTable);
      const sum = this.singleTable.reduce((acc, coord) => {
        acc.x += coord.NodeX;
        acc.y += coord.NodeY;
        return acc;
      }, { x: 0, y: 0 });

      return {
        x: sum.x / totalCoords,
        y: sum.y / totalCoords
      };
    }
    },
    methods: {
        inicio() {
            console.log("ENTRO");
            this.maxZoom = this.mapa.infoMapa.zoom.max;
            this.minZoom = this.mapa.infoMapa.zoom.min;
        },
        getCoords(x, y) {
            return L.latLng(y, x);
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
        }
    }
};
</script>
