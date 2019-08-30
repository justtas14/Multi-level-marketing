import Vue from 'vue';
import store from './store/index';
import ModalGalleryWrapper from "./Components/ModalGalleryWrapper";
import EventBus from './EventBus/EventBus';

const modal = new Vue({
    el: '#modal',
    store,
    data: {
        onFileSelect: () => {},
        onFileRemove: () => {},
        onFileRemoveList: [],
        mqls: [
            window.matchMedia('(max-width: 550px)'),
            window.matchMedia('(max-width: 1350px)'),
        ],
    },
    methods: {
        showModal: function (category, onFileSelect, onFileRemove) {
            this.onFileSelect = onFileSelect;
            this.onFileRemoveList.push(onFileRemove);
            store.commit('gallery/changeModalState', true);
            store.commit('gallery/changeFilesPerPage', 21);
            store.commit('gallery/changeCategory', category);

            for (let i=0; i < this.mqls.length; i++) {
                this.mqls[i].addListener(this.mediaQuerryResponse);
            }

            const w = window.innerWidth;


            store.commit('gallery/setSpinnerState', true);

            if (w > 1350) {
                store.dispatch('gallery/callDataAxios', 21);
            } else if (w > 550 && w <= 1350) {
                store.dispatch('gallery/callDataAxios', 15);
            } else {
                store.dispatch('gallery/callDataAxios', 14);
            }
        },
        mediaQuerryResponse: function () {
            if (this.mqls[0].matches) {
                store.dispatch('gallery/callDataAxios', 14);
            } else if (this.mqls[1].matches) {
                store.dispatch('gallery/callDataAxios', 15);
            } else {
                store.dispatch('gallery/callDataAxios', 21);
            }
        }
    },
    mounted() {
        EventBus.$on('oneClickFile',  (fileId, fileName, filePath, downloadPath) => {
            const fileObj = {
                fileId: fileId,
                fileName: fileName,
                filePath: filePath
            };
            this.onFileSelect(fileObj);
        });
        EventBus.$on('checkDeleted',  (fileId) => {
            this.onFileRemoveList.forEach((fun) => {
                if (fun(true) == fileId) {
                    fun();
                }
            })
        });

    },
    template: '<ModalGalleryWrapper/>',

    components: {
        ModalGalleryWrapper
    }
});

window.modal = modal;