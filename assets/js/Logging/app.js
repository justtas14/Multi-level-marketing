import Vue from 'vue';
import Logging from "./Components/Logging";

Vue.config.productionTip = false;

export const loggingFun = () => {
    const logging = new Vue({
        el: '#logging',
        data: {

        },
        methods: {},
        template: '<Logging/>',
        components: {
            Logging
        },
    });
};


