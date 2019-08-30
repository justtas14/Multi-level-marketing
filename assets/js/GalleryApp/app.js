import Vue from 'vue';
import store from './store/index';
import GalleryWrapper from './Components/GalleryWrapper';
import EventBus from "./EventBus/EventBus";

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
    },
    mounted() {
        EventBus.$on('oneClickFile',  (fileId, fileName, filePath, downloadPath) => {
            console.log(downloadPath);
            window.location.href = downloadPath;
        });
    }
});

window.gallery = gallery;

