import Vue from 'vue';
import Clipboard from "./Components/clipboard";
import VueClipboards from 'vue-clipboards';

Vue.config.productionTip = false;
Vue.use(VueClipboards);

export const clipboardFun = (invitationUrl) => {
    const ClipBoard = new Vue({
        el: '#clip-board',
        data: {
            invitationUrl: invitationUrl
        },
        methods: {},
        template: '<Clipboard v-bind:invitationUrl="invitationUrl"/>',
        components: {
            Clipboard
        },
    });
};


