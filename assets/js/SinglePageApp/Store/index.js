import Vue from 'vue';
import Vuex from 'vuex';
import UserSearch from './modules/user-search'
import Login from './modules/security'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        UserSearch : {
            namespaced: true,
            ...UserSearch
        },
        Login: {
            namespaced: true,
            ...Login
        }

    }
})