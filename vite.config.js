import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
// import RubyPlugin from 'vite-plugin-ruby';
// import inject from "@rollup/plugin-inject";

// const path = require('path');

export default defineConfig({
  plugins: [
    // inject({   // => that should be first under plugins array
    //   $: 'jquery',
    //   jQuery: 'jquery',
    // }),
    // RubyPlugin(),
    laravel({
      input: [
        // 'resources/js/csdb/detail.js',
        'resources/css/app.css',
        'resources/css/loadingbar.css',
        'resources/css/dmodule.css',
        'resources/js/csdb/CsdbReader.js',
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
      'vue': 'vue/dist/vue.esm-bundler.js',
    }
  }
});