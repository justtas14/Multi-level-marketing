import Vue from 'vue';
import store from './store/index';
import ModalGalleryWrapper from "./Components/ModalGalleryWrapper";
import EventBus from './EventBus/EventBus';

const modal = new Vue({
    el: '#modal',
    store,
    data: {
        formFieldSelector: '',
        fileNameSelector: ''
    },
    methods: {
        showModal: function (category, formFieldSelector, fileNameSelector) {
            this.formFieldSelector = formFieldSelector;
            this.fileNameSelector = fileNameSelector;
            store.commit('gallery/changeModalState', true);
            store.commit('gallery/changeFilesPerPage', 21);
            store.commit('gallery/changeCategory', category);
            let mql1 = window.matchMedia('(max-width: 1350px)');
            mql1.addListener((e) => {
                if (e.matches) {
                    store.dispatch('gallery/callDataAxios', 15);
                } else {
                    store.dispatch('gallery/callDataAxios', 21);
                }
            });
            let w = window.innerWidth;

            if (w > 1350) {
                store.dispatch('gallery/callDataAxios', 21);
            } else {
                store.dispatch('gallery/callDataAxios', 15);
            }
        },
    },
    mounted() {
        EventBus.$on('oneClickFile',  (fileId, fileName) => {
            let hiddenInput = document.querySelector(this.formFieldSelector);
            hiddenInput.setAttribute("value", fileId);
            let mainLogoFileContainer = document.querySelector(this.fileNameSelector);
            mainLogoFileContainer.innerHTML = fileName;
        });
    },

    template: '<ModalGalleryWrapper/>',

    components: {
        ModalGalleryWrapper
    }
});

window.modal = modal;