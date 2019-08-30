import Vue from 'vue';
import Vuex from 'vuex';
import gallery from './modules/gallery'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        gallery : {
            namespaced: true,
            ...gallery
        }
    }
})