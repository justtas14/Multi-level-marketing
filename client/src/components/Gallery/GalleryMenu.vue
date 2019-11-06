<template>
    <div class="categoryMenu">
        <div class="gallery-title">
            <div class="card-title">File gallery</div>
                <input class="file-upload-input" id="files" type='file'  @change="readUrl"/>
                <label for="files"><i class="fa fa-plus" aria-hidden="true"></i></label>
        </div>
        <div id="material-tabs">
            <div id="dropdownTab" @click="tabsVisible=!tabsVisible">
                <span class="categoryName"> <i class="material-icons categoryIcons" :class="{reverse: tabsVisible}">keyboard_arrow_down</i> Category </span>
            </div>
            <transition name="fadeIn">
                <a v-show="tabsVisible || !smallWindow" id="tab1-tab" @click="$emit('categorise-files', 'all')"  :class="{'active': category === 'all' }">
                    <span class="categoryName"><i class="material-icons categoryIcons">dynamic_feed</i>All files</span>
                </a>
            </transition>
            <transition name="fadeIn">
                <a v-show="tabsVisible || !smallWindow" id="tab2-tab" @click="$emit('categorise-files', 'images')" :class="{'active': category === 'images' }">
                    <span class="categoryName" style="margin-left: 14px"><i class="material-icons categoryIcons">image</i>Images</span>
                </a>
            </transition>
            <transition name="fadeIn">
                <a v-show="tabsVisible || !smallWindow" id="tab3-tab" @click="$emit('categorise-files', 'files')" :class="{'active': category === 'files' }">
                    <span class="categoryName"><i class="material-icons categoryIcons">insert_drive_file</i>Files</span>
                </a>
            </transition>
            <span class="yellow-bar"></span>
        </div>
    </div>
</template>
<script>
    import './css/GalleryMenu.scss';

    export default {
        name: 'CategoryMenu',
        props: ['category'],
        components: {
        },
        data() {
            return {
                tabsVisible: false,
                smallWindow: false
            }
        },
        methods: {
            readUrl: function (e) {
                this.$emit('readUrl', e);
            },
        },
        created() {
            const mql1 = window.matchMedia('(max-width: 1000px)');
            mql1.addListener((e) => {
                if (e.matches) {
                    this.smallWindow = true;
                } else {
                    this.smallWindow = false;
                }
            });

            const w = window.innerWidth;

            if (w > 1000) {
                this.smallWindow = false;
            } else {
                this.smallWindow = true;
            }
        }
    }
</script>

<style scoped>

</style>