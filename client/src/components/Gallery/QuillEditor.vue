<template>
    <div id="quillApp">
        <quill v-model="content" :config="config" ref="quillEditor"></quill>
        <ModalGalleryWrapper/>
    </div>
</template>

<script>
import Vue from 'vue';
import axios from 'axios';
import VueQuill from 'vue-quill';
import { QuillDeltaToHtmlConverter } from 'quill-delta-to-html';
import { mapActions, mapMutations } from 'vuex';
import ModalGalleryWrapper from './ModalGalleryWrapper.vue';
import constants from './constants/constants';
import EventBus from './EventBus/EventBus';

Vue.use(VueQuill);

export default {
    name: 'QuillEditor',
    props: [],
    components: {
        ModalGalleryWrapper,
    },
    data() {
        return {
            scope: this,
            content: {
                ops: [],
            },
            mqls: [
                window.matchMedia('(max-width: 550px)'),
                window.matchMedia('(max-width: 1350px)'),
            ],
            config: {
                modules: {
                    syntax: true,
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
                                this.changeModalState(true);
                                this.changeFilesPerPage(21);
                                this.changeCategory('images');

                                for (let i = 0; i < this.mqls.length; i += 1) {
                                    this.mqls[i].addListener(this.mediaQuerryResponse);
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
        const inputData = document.querySelector('input[type=hidden]').value;
        const quillEditor = document.querySelector('.ql-editor');
        let quillHTML;

        try {
            this.content = JSON.parse(inputData);
            const cfg = {
                inlineStyles: true,
                allowBackgroundClasses: true,
            };
            const converter = new QuillDeltaToHtmlConverter(this.content.ops, cfg);
            quillHTML = converter.convert();
        } catch (e) {
            quillHTML = inputData;
        }
        quillEditor.innerHTML = quillHTML;

        const form = document.querySelector('form');
        form.addEventListener('submit', this.callback, false);

        EventBus.$on('oneClickFile', (fileId, fileName, filePath) => {
            axios.post(constants.api.uploadEditorFile, filePath).then((res) => {
                const url = res.data;
                const range = this.editor.getSelection;
                this.editor.insertEmbed(range.index, 'image', `${url}`);
            }).catch((err) => {
                console.log(err);
            });
        });
    },
    computed: {
        editor() {
            return this.$refs.quillEditor.editor;
        },
    },
    methods: {
        callback() {
            const emailBody = document.querySelector('input[type=hidden]');
            emailBody.value = JSON.stringify(this.content);
            return true;
        },
        mediaQuerryResponse() {
            if (this.mqls[0].matches) {
                this.callDataAxios(14);
            } else if (this.mqls[1].matches) {
                this.callDataAxios(15);
            } else {
                this.callDataAxios(21);
            }
        },
        ...mapActions('Gallery', [
            'callDataAxios',
        ]),
        ...mapMutations('Gallery', [
            'changeModalState',
            'changeCategory',
            'changeFilesPerPage',
            'changeEditorState',
            'changeDataLoadState',
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
