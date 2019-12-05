<template>
    <div id="modalWrapper">
        <div id="myGalleryModal" class="modal">
            <div class="modal-content">
                <a class="styles_closeButton__20ID4 closeGallery" @click="closeShowModal(false)">
                    <svg class="styles_closeIcon__1QwbI" xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 36 36">
                        <path d="M28.5 9.62L26.38 7.5 18 15.88 9.62 7.5 7.5 9.62 15.88
                         18 7.5 26.38l2.12 2.12L18 20.12l8.38 8.38 2.12-2.12L20.12 18z">
                        </path>
                    </svg>
                </a>
                <slot name= "fileInputContainer">
                </slot>
                <hr class="line" align="left">
                <div id="previewBodyContent" class="modal-body-gallery">
                    <slot name = "gallery">
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';

export default {
    name: 'ModalWrapper',
    props: [],
    components: {
    },
    data() {
        return {
        };
    },
    mounted() {
        const galleryModal = document.getElementById('myGalleryModal');
        window.addEventListener('click', (event) => {
            if (event.target === galleryModal) {
                this.closeShowModal(false);
            }
        });
    },

    computed: {
        ...mapState('Gallery', {
            modalState: 'modalState',
        }),
    },

    methods: {
        ...mapMutations('Gallery', [
            'changeModalState',
            'changePage',
        ]),
        closeShowModal(flag) {
            this.changeModalState(flag);
            const page = 1;
            const action = null;
            this.changePage({
                page,
                action,
            });
        },
    },
};
</script>

<style lang="scss" scoped>
    @import './css/ModalWrapper.scss';
</style>
