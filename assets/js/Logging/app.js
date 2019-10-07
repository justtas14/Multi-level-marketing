import Vue from 'vue';
import store from './store/index';
import Logging from "./Components/Logging";

Vue.config.productionTip = false;

const logging = new Vue({
    el: '#logging',
    store,
    data: {

    },
    methods: {},
    template: '<Logging/>',
    components: {
        Logging
    },
});


