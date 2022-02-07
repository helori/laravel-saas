window._ = require('lodash');
//window.$ = window.jQuery = require('jquery');

window.axios = require('axios');
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'accept': 'application/json',
};

import { createApp, h, ref } from 'vue';
import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router';

import AppLayout from './Pages/AppLayout.vue'

import routesSaas from './routes-saas.js'
import routesApp from './routes-app.js'
import filters from './filters.js'

import resolveConfig from 'tailwindcss/resolveConfig'
import tailwindConfig from '../../tailwind.config.js'
window.tailwindcss = resolveConfig(tailwindConfig)

if(document.getElementById("vue-app"))
{
    const routes = ([]).concat(routesSaas, routesApp);

    const router = createRouter({
        history: createWebHashHistory(),
        routes: routes,
    })

    const app = createApp({

        components: {
            AppLayout,
        },

    });

    app.config.globalProperties.$filters = filters;
    app.use(router).mount('#vue-app');
}

if(document.getElementById("auth-app"))
{
    createApp({

        setup(){
            const showRecoveryCodeForm = ref(false);

            return {
                showRecoveryCodeForm,
            }
        }

    }).mount('#auth-app');
}
