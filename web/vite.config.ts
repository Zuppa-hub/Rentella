import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  server: {    // <-- this object is added
    host: true,
    port: 8000
  },
  plugins: [vue()]
})