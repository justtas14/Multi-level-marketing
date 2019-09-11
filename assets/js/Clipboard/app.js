import Vue from 'vue';
import Clipboard from "./Components/clipboard";
import VueClipboards from 'vue-clipboards';

Vue.use(VueClipboards);

const ClipBoard = new Vue({
    el: '#clip-board',
    data: {

    },
    methods: {

    },
    template: '<Clipboard/>',
    components: {
        Clipboard
    },
});


