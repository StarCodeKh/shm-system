import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'

const root = process.cwd()

export default defineConfig({
    plugins: [
        laravel({
            // Order is the cascade order: component base styles, Tailwind utilities, then the app's own styles
            input: ['resources/assets/css/vuesax.css', 'node_modules/vue-select/dist/vue-select.css', 'resources/assets/css/main.css', 'resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                // Keep /images/... and other public paths as written (served from public/, not bundled)
                transformAssetUrls: { base: null, includeAbsolute: false },
            },
        }),
    ],
    build: {
        rolldownOptions: {
            // Production bundles leave out debug output (console.log / debug / info / trace);
            // console.error and console.warn stay so real failures still reach the browser console
            treeshake: {
                manualPureFunctions: ['console.log', 'console.debug', 'console.info', 'console.trace'],
            },
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(root, 'resources/js/src'),
            '@assets': path.resolve(root, 'resources/assets'),
            '@css': path.resolve(root, 'resources/css'),
        },
    },
})
