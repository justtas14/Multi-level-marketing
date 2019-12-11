<template>
    <div id="quillApp">
        <quill v-model="content" v-on:input="updateValue"
         :config="config" ref="quillEditor"></quill>
        <ModalGalleryWrapper v-bind:style="{display: modalDisplay}"
         @closeModal="modalDisplay='none'"/>
    </div>
</template>

<script>
import Vue from 'vue';
import VueQuill from 'vue-quill';
import { mapActions, mapMutations, mapState } from 'vuex';
import ModalGalleryWrapper from './ModalGalleryWrapper.vue';
import modalGalleryConst from './constants/modalGalleryConst';
import '../../assets/css/quill.snow.css';
import DeltaToHtml from './Services/deltaToHtml';

Vue.use(VueQuill);

export default {
    name: 'QuillEditor',
    props: ['configurationContent'],
    components: {
        ModalGalleryWrapper,
    },
    data() {
        return {
            scope: this,
            modalDisplay: 'none',
            content: {
                ops: [],
            },
            config: {
                modules: {
                    syntax: false,
                    toolbar: {
                        container: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ color: [] }],
                            [{ script: 'super' }, { script: 'sub' }],
                            [{ header: '1' }, { header: '2' }, 'blockquote', 'code-block'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['direction'],
                            ['link', 'image'],
                            ['clean'],
                        ],
                        handlers: {
                            image: () => {
                                this.modalDisplay = 'block';
                                this.changeFilesPerPage(21);
                                this.changeCategory('images');

                                for (let i = 0; i < modalGalleryConst.mqls.length; i += 1) {
                                    modalGalleryConst.mqls[i].addListener(this.mediaQuerryResponse);
                                }

                                const w = window.innerWidth;

                                if (w > 1350) {
                                    this.callDataAxios(21);
                                } else if (w > 550 && w <= 1350) {
                                    this.callDataAxios(15);
                                } else {
                                    this.callDataAxios(14);
                                }
                                this.changeDataLoadState(true);
                            },
                        },
                    },
                },
                theme: 'snow',
            },
        };
    },
    mounted() {
        const quillEditor = document.querySelector('.ql-editor');
        const deltaToHtmlObj = new DeltaToHtml(this.configurationContent, this.content);
        quillEditor.innerHTML = deltaToHtmlObj.deltaToHtml();

        this.changeClickFile((fileId, fileName, fileSrc) => {
            this.modalDisplay = 'none';
            const range = this.editor.getSelection;
            this.editor.insertEmbed(range.index, 'image', `${fileSrc}`);
        });
    },
    computed: {
        editor() {
            return this.$refs.quillEditor.editor;
        },

        ...mapState('Security', [
            'token',
        ]),
    },
    methods: {
        updateValue() {
            this.$emit('input', JSON.stringify(this.content));
        },
        mediaQuerryResponse() {
            if (modalGalleryConst.mqls[0].matches) {
                this.callDataAxios(14);
            } else if (modalGalleryConst.mqls[1].matches) {
                this.callDataAxios(15);
            } else {
                this.callDataAxios(21);
            }
        },
        ...mapActions('Gallery', [
            'callDataAxios',
        ]),
        ...mapMutations('Gallery', [
            'changeCategory',
            'changeFilesPerPage',
            'changeDataLoadState',
            'changeClickFile',
        ]),
    },
    created() {

    },
    beforeMount() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/QuillEditor.scss';
</style>
