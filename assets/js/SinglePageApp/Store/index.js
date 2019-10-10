import Vue from 'vue';
import Vuex from 'vuex';
import UserSearch from './modules/user-search'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        UserSearch : {
            namespaced: true,
            ...UserSearch
        }
    }
})