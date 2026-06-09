import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
            ],
            refresh: true,
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
    server: {
        // host: '0.0.0.0',
        cors: true,
        hmr: {
            host: 'localhost',
            // host: '10.91.233.144',
        }
    },
    build: {
        target: 'esnext', // Atau 'es2020'
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('apexcharts')) {
                            return 'vendor-charts';
                        }
                        if (id.includes('firebase')) {
                            return 'vendor-firebase';
                        }
                        return 'vendor'; // Library lain jadi satu di vendor biasa
                    }
                }
            }
        },
        minify: 'esbuild',
        sourcemap: false,
    },
});
