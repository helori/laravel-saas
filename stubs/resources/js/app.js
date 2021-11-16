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
import Dashboard from './Pages/Dashboard.vue'
import Profile from './Pages/Profile.vue'
import Team from './Pages/Team.vue'
import Billing from './Pages/Billing.vue'

import mitt from 'mitt';
const emitter = mitt();

import routesSaas from './routes-saas.js'
import filters from './filters.js'

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

    app.config.globalProperties.emitter = emitter;
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
