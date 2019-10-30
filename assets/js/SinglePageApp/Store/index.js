import Vue from 'vue';
import Vuex from 'vuex';
import UserSearch from './modules/user-search'
import Security from './modules/security'
import Gallery from './modules/gallery'
import Logs from "./modules/logs"
import Sidebar from "./modules/sidebar"

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        UserSearch: {
            namespaced: true,
            ...UserSearch
        },
        Security: {
            namespaced: true,
            ...Security
        },
        Gallery: {
            namespaced: true,
            ...Gallery
        },
        Logs: {
            namespaced: true,
            ...Logs
        },
        Sidebar: {
            namespaced: true,
            ...Sidebar
        }
    }
})