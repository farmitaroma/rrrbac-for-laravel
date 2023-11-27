import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/css/rrrbac.css',
        })
    ],
    build: {
        manifest: false,
        outDir: 'dist',
    }
})