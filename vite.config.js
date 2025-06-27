import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/main.js',
                'resources/js/public-calc.js',
                'resources/css/overrides.css',
            ],
            refresh: true,
        }),
        vue(),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                main: 'resources/js/main.js',
                publicCalc: 'resources/js/public-calc.js',
                style: 'resources/css/app.css',
                overrides: 'resources/css/overrides.css',
            },
        },
    },
})
