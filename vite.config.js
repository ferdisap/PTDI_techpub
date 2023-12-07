import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

// const path = require('path');

export default defineConfig({
  plugins: [
    laravel({
      input: [
        // 'resources/js/csdb/detail.js',
        'resources/js/csdb/CsdbReader.js',
        'resources/css/app.css',
        'resources/js/ietm/app.js',
        'resources/views/**/*.vue'
      ],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
            base: null,
            includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': '/resources/js',
      '@@': '/resources/views',
      'vue': 'vue/dist/vue.esm-bundler.js',
    }
  }
});