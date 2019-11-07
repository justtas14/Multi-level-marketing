import Vue from 'vue';
import { MdField } from 'vue-material/dist/components';
import VueClipboards from 'vue-clipboards';
import VueQuill from 'vue-quill';
import App from './App.vue';
import './registerServiceWorker';
import router from './router';
import store from './store';

Vue.config.productionTip = false;
Vue.use(VueQuill);
Vue.use(MdField);
Vue.use(VueClipboards);

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount('#main');
