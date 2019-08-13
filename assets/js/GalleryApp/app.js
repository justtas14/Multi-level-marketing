import Vue from 'vue';
import store from './store/index';
import GalleryWrapper from './Components/GalleryWrapper';

const gallery = new Vue({
    el: '#gallery',
    store,
    data: {
    },
    methods: {
    },
    template: '<GalleryWrapper/>',
    components: {
        GalleryWrapper,
    }
});

window.gallery = gallery;

