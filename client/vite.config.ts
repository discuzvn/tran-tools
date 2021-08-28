import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import WindiCSS from 'vite-plugin-windicss'
import ViteComponents from 'vite-plugin-components'
import ViteIcons, { ViteIconsResolver } from 'vite-plugin-icons'

// https://vitejs.dev/config/
export default defineConfig({
    resolve: {
        alias: {
            '~/': `${path.resolve(__dirname, 'src')}/`,
        },
    },
    plugins: [
        vue(),
        WindiCSS(),
        ViteComponents({
            globalComponentsDeclaration: true,
            customComponentResolvers: [
                // https://github.com/antfu/vite-plugin-icons
                ViteIconsResolver({
                    componentPrefix: '',
                    // enabledCollections: ['carbon']
                }),
            ],
        }),
        ViteIcons(),
    ]
})
