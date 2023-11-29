import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel([
            // 'resources/js/csdb/detail.js',
            'resources/js/csdb/CsdbReader.js',
        ]),
    ],
});