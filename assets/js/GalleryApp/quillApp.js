import Vue from 'vue';
import store from './store/index';
import QuillEditor from './Components/QuillEditor'
import VueQuill from 'vue-quill';

Vue.use(VueQuill);

const quillEditor = new Vue({
    el: '#quillEditor',
    store,
    data: {
    },
    template: '<QuillEditor/>',
    components: {
        QuillEditor
    }
});