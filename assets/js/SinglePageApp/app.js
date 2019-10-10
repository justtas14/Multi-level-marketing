import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../SinglePageApp/Store';
import Main from './Components/Main';
import { MdField } from 'vue-material/dist/components';

Vue.config.productionTip = false;
Vue.use(VueRouter);
Vue.use(MdField);

const routes = [
    { path: '/associate', component: AssociateHome },
    { path: '/associate/profile', component: AssociateProfile},
    { path: '/associate/invite', component: AssociateInvitation },
    { path: '/associate/viewer', component: AssociateTeamViewer}
];

const router = new VueRouter({
    mode: 'history',
    routes,
    base: '/'
});


export const mainFun = (el) => {
    const main = new Vue({
        el: el,
        store,
        router,
        data: {
        },
        methods: {
        },
        template: '<Main/>',
        components: {
            Main,
        },
        mounted() {

        }
    });
};



