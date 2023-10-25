<template>
    <div class="map-container">
        <!-- Vue component that renders a map using the Leaflet library.-->
        <l-map :use-global-leaflet="false" v-model:zoom="zoom" :center="[myLatitude, myLongitude]"
            :options="{ zoomControl: false, }">
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
                name="OpenStreetMap"></l-tile-layer>
            <l-control-zoom :position="'bottomright'"></l-control-zoom>
            <!-- Utilizza v-for per creare marker e popup per ogni elemento in apiData -->
            <div v-if="apiData[0].name == undefined">
                <l-marker v-for="(item, index) in apiData" :key="index"
                    :lat-lng="[item.beach.latitude, item.beach.longitude]">
                    <l-popup>{{ item.beach.name }}</l-popup>
                </l-marker>
            </div>
            <div v-else>
                <l-marker v-for="(item, index) in apiData" :key="index" :lat-lng="[item.latitude, item.longitude]">
                    <l-popup>{{ item.name }}</l-popup>
                </l-marker>
            </div>
        </l-map>
    </div>
</template>

<script lang="ts">
import "leaflet/dist/leaflet.css";
import { Geolocate } from "../apiService";
import { LMap, LTileLayer, LMarker, LPopup, LControlZoom } from "@vue-leaflet/vue-leaflet";
export default {
    name: "leaflet-map",
    components: {
        // The lines `LMap, LTileLayer, LMarker, LPopup, LControlZoom` are importing specific components from
        // the `@vue-leaflet/vue-leaflet` library. These components are used to render a map, tile layers,
        // markers, popups, and zoom controls in the Vue.js application. By importing these components, they
        // can be used in the template section of the Vue component to create the desired map functionality.
        LMap,
        LTileLayer,
        LMarker,
        LPopup,
        LControlZoom,
    },
    props: {
        apiData: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            zoom: 10,
            myLatitude: 0,
            myLongitude: 0,
        }
    },
    created() {
        Geolocate()
            .then((coordinates) => {
                this.myLatitude = coordinates.latitude;
                this.myLongitude = coordinates.longitude
            })
            .catch((error) => {
                console.error("Errore durante la geolocalizzazione:", error);
            });
    },
}
</script>
