window._ = require('lodash');
//window.$ = window.jQuery = require('jquery');

window.axios = require('axios');
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'accept': 'application/json',
};

import { createApp, h, ref, provide } from 'vue';
import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router';

import AppLayout from './Pages/AppLayout.vue'

import Loader from './Components/Loader'
import Alert from './Components/Alert'
import DialogModal from './Components/DialogModal'

import RequestError from './Components/RequestError'
import RequestStatus from './Components/RequestStatus'
import RequestState from './Components/RequestState'

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

        setup(){
            const dialogMessage = ref(null);
            const dialogMessageData = ref({});

            function openDialogMessage(data)
            {
                dialogMessageData.value = {...{
                    title: 'Message',
                    message: 'Message...',
                    maxWidthClass: 'max-w-screen-md',
                    closeText: 'Fermer',
                }, ...data};
                dialogMessageData.value.headerClass = headerClassFromType(dialogMessageData.value.type);
                dialogMessage.value.open();
            }

            function closeDialogMessage()
            {
                dialogMessage.value.close();
            }

            provide('openDialogMessage', openDialogMessage);

            function headerClassFromType(type)
            {
                if(type === 'success') { return 'bg-green-500'; }
                if(type === 'error') { return 'bg-red-500'; }
                if(type === 'danger') { return 'bg-red-500'; }
                if(type === 'warning') { return 'bg-yellow-500'; }
                if(type === 'info') { return 'bg-blue-500'; }
                if(type === 'default') { return 'bg-primary'; }
                return 'bg-gray-100';
            }

            return {
                dialogMessage,
                dialogMessageData,
                openDialogMessage,
                closeDialogMessage,
            }
        }

    });

    app.config.globalProperties.$filters = filters;
    
    app.component('loader', Loader);
    app.component('alert', Alert);
    app.component('dialog-modal', DialogModal);

    app.component('request-error', RequestError);
    app.component('request-status', RequestStatus);
    app.component('request-state', RequestState);
    
    //app.provide('openDialogMessage', openDialogMessage);
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
