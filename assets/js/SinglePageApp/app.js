import Vue from 'vue';
import router from "./Router/index";
import store from '../SinglePageApp/Store';
import Main from './Pages/Main/Components/Main';
import { MdField } from 'vue-material/dist/components';
import VueQuill from 'vue-quill';

Vue.config.productionTip = false;
Vue.use(MdField);
Vue.use(VueQuill);

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



