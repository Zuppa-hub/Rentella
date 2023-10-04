<script lang="ts">
import TopBar from '@/components/TopBar.vue';
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer } from "@vue-leaflet/vue-leaflet";
export default {
  name: "Home",
  components: {
    LMap,
    LTileLayer,
    TopBar
  },
  data() {
    return {
      zoom: 10,
    };
  },
};
</script>

<template>
  <body class="h-screen">
    <TopBar />
    <div class="flex flex-row h-full">
      <div class="md:w-2/5 dark:bg-gray-950 overflow-y-auto w-full">
        <!-- Contenuto della prima colonna -->
        <!-- Testo centrato sotto la linea nera -->
        <div class="flex items-center justify-center  dark:text-white">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut nunc sit amet lacus dignissim facilisis at dictum
          tellus. Ut vel nulla at diam interdum rutrum non vel felis. Quisque ac velit egestas, pretium est eget, rutrum
          nibh. Aenean eleifend sagittis elit, sit amet porttitor nulla pellentesque sed. Duis gravida arcu sed enim
          imperdiet, vel ultricies libero rutrum. Proin volutpat urna nec elementum accumsan. Proin ac sem erat. Mauris
          aliquam aliquet congue.

          Ut tortor nibh, egestas at semper vel, cursus id elit. Nullam accumsan lectus ipsum, sed pulvinar sem varius in.
          Etiam eu sapien lectus. Nam lectus arcu, rhoncus eget velit feugiat, cursus sagittis lacus. Nunc placerat tempus
          massa, et vehicula turpis. Nam diam massa, hendrerit at laoreet luctus, pulvinar in arcu. Etiam quis orci eu
          urna aliquam pulvinar vel eget augue. Nunc vitae justo eget massa cursus laoreet. Donec pretium posuere ipsum a
          lobortis. Nam vehicula suscipit justo quis congue. Nulla facilisi. Mauris pretium lacus at viverra aliquam.
          Pellentesque imperdiet tellus vitae ullamcorper tincidunt. Fusce in magna at dui aliquam placerat pulvinar ut
          ipsum. Nam porttitor vitae diam et posuere.

          Nam vitae euismod mauris, condimentum viverra enim. Quisque posuere feugiat sollicitudin. Maecenas lorem nisl,
          elementum non egestas vitae, imperdiet eu turpis. Vivamus sollicitudin orci eu hendrerit convallis. Cras eget
          risus a lorem feugiat porttitor. Maecenas commodo enim elit, id dictum metus eleifend sit amet. Mauris in erat a
          dolor molestie ultricies in eget arcu. Fusce ullamcorper, risus luctus malesuada malesuada, nibh leo vehicula
          arcu, nec varius eros leo vitae tellus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere
          cubilia curae; Nam dictum blandit dui id commodo. Ut vel velit vel augue sodales pretium id ac sem. Morbi quis
          lacinia ante, at ullamcorper dolor.

          Nam diam magna, euismod viverra consequat in, vehicula non enim. Sed ornare tristique orci ut pellentesque. Sed
          iaculis sem at laoreet finibus. Donec ante mauris, rutrum sed urna nec, gravida commodo libero. Nam auctor
          iaculis eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc
          dapibus eu erat vitae congue. Morbi fringilla nisi massa. Cras vestibulum, urna non sagittis iaculis, orci ante
          sollicitudin leo, id ullamcorper quam mauris tristique neque. Nam eleifend sem nibh, et dapibus justo convallis
          ut. In hac habitasse platea dictumst.

          Donec mollis magna eget facilisis vestibulum. Cras in tortor hendrerit est pharetra finibus eu in neque. In
          dapibus libero id nisl sollicitudin, ut congue magna accumsan. Vestibulum sem orci, pretium nec quam id,
          malesuada scelerisque tellus. Aliquam placerat ex sollicitudin, dignissim justo faucibus, consectetur nisl. Sed
          tincidunt, lacus ac blandit lobortis, orci orci consectetur nisl, vitae rhoncus urna velit dignissim tellus.
          Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nam ac pretium ante.
          Integer dapibus placerat tincidu
        </div>
      </div>
      <div class="hidden md:flex flex-3 md:w-4/5 bg-cover bg-center">
        <l-map ref="map" v-model:zoom="zoom" :center="[47.41322, -1.219482]">
          <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
            name="OpenStreetMap"></l-tile-layer>
        </l-map>

      </div>
    </div>
  </body>
</template>
