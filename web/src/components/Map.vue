<template>
    <div class="map-container">
        <!-- Vue component that renders a map using the Leaflet library.--> 
        <l-map ref="map" v-model:zoom="zoom" :center="[myLatitude, myLongitude]" :options="{ zoomControl: false, }">
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
                name="OpenStreetMap"></l-tile-layer>
            <l-control-zoom :position="'bottomright'"></l-control-zoom>
            <!-- Utilizza v-for per creare marker e popup per ogni elemento in apiData -->
            <l-marker v-for="(item, index) in apiData" :key="index" :lat-lng="[item.latitude, item.longitude]">
                <l-popup>{{ item.city_name }}</l-popup>
            </l-marker>
        </l-map>
    </div>
</template>

<script lang="ts">
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LMarker, LPopup, LControlZoom } from "@vue-leaflet/vue-leaflet";
export default {
    name: "map",
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
            type: Array,
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
    methods: {
        Geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    this.myLatitude = position.coords.latitude;
                    this.myLongitude = position.coords.longitude;
                    console.log(this.myLatitude, this.myLongitude);
                });
            }
        }
    },
    created() {
       // The code `if (this.myLatitude === 0 && this.myLongitude === 0) { this.Geolocate(); }` is
       // checking if the latitude and longitude values are both equal to 0. If they are, it means that
       // the geolocation has not been retrieved yet. In this case, the `Geolocate()` method is called
       // to retrieve the current position using the `navigator.geolocation.getCurrentPosition()`
       // function. Once the position is obtained, the latitude and longitude values are updated, and
       // the map will be centered on the user's current location.
        if (this.myLatitude === 0 && this.myLongitude === 0) {
            this.Geolocate();
        }
    },
}
</script>
