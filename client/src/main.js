import Vue from 'vue';
import { MdField } from 'vue-material/dist/components';
import VueClipboards from 'vue-clipboards';
import VueQuill from 'vue-quill';
import VuexPersist from './store/plugins/VuexPersist';
import App from './App.vue';
import './registerServiceWorker';
import router from './router';
import store from './store';
import UserSearch from './store/modules/user-search';
import Security from './store/modules/security';
import Gallery from './store/modules/gallery';
import Logs from './store/modules/logs';
import Sidebar from './store/modules/sidebar';
import AdminHome from './store/modules/adminHome';
import AssociateHome from './store/modules/associateHome';
import Invitation from './store/modules/invitation';

store.registerModule('UserSearch', UserSearch);
store.registerModule('Security', Security);
store.registerModule('Gallery', Gallery);
store.registerModule('Logs', Logs);
store.registerModule('Sidebar', Sidebar);
store.registerModule('AdminHome', AdminHome);
store.registerModule('AssociateHome', AssociateHome);
store.registerModule('Invitation', Invitation);

VuexPersist(store);

Vue.config.productionTip = false;
Vue.use(VueQuill);
Vue.use(MdField);
Vue.use(VueClipboards);

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount('#app');
