import Vue from 'vue';
import store from './store/index';
import Main from './Components/Main';
import { MdField } from 'vue-material/dist/components';

Vue.config.productionTip = false;
Vue.use(MdField);

export const userSearchFun = (el, mainAction, mainActionLabel) => {
    const UserSearch = new Vue({
        el: el,
        store,
        data: {
            mainAction: mainAction,
            mainActionLabel: mainActionLabel
        },
        methods: {
        },
        template: '<Main v-bind:mainAction="mainAction" v-bind:mainActionLabel="mainActionLabel"/>',
        components: {
            Main,
        },
        mounted() {

        }
    });
};



