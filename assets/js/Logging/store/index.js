import Vue from 'vue';
import Vuex from 'vuex';
import Logs from './modules/logs'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        Logs : {
            namespaced: true,
            ...Logs
        }
    }
})