import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';

export default defineConfig({
    build: {
        chunkSizeWarningLimit: 950, // Setzen Sie dies auf den gewünschten Wert
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/js/app.js',
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
        {
            name: 'polyfill-node-globals',
            setup(build) {
                build.onLoad({ filter: /\.[jt]s$/ }, async (args) => {
                    const contents = await fs.promises.readFile(args.path, 'utf8');
                    return {
                        loader: args.path.endsWith('.ts') ? 'ts' : 'js',
                        contents: contents
                            .replace(/process\.env/g, 'import.meta.env')
                            .replace(/require\("buffer"\)/g, 'import("buffer")')
                            .replace(/require\("process"\)/g, 'import("process")')
                    };
                });
            }
        }
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            process: 'process/browser',
            buffer: 'buffer'
        },
    },
});
