import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
    resolve: {
        alias: {
            // The root app in resources/js/app.js mounts onto Blade-rendered
            // markup instead of an SFC template (each layout's #app div has
            // its own server-rendered child component tags) - that needs
            // Vue's runtime compiler, which the default runtime-only build
            // Vite otherwise resolves to does not include.
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
})
