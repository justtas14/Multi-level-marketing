<template>
    <div class="categoryMenu">
        <div class="gallery-title">
            <div class="card-title">File gallery</div>
            <input class="file-upload-input" id="files" type='file'  @change="readUrl"/>
            <label for="files"><i class="fa fa-plus" aria-hidden="true"></i></label>
        </div>
        <div id="material-tabs">
            <a id="tab1-tab" @click="$emit('categorise-files', 'all')"  :class="{'active': category === 'all' }">
                <span class="categoryName"><i class="material-icons categoryIcons">dynamic_feed</i>All files</span>
            </a>
            <a id="tab2-tab" @click="$emit('categorise-files', 'images')" :class="{'active': category === 'images' }">
                <span class="categoryName" style="margin-left: 14px"><i class="material-icons categoryIcons">image</i>Images</span>
            </a>
            <a id="tab3-tab" @click="$emit('categorise-files', 'files')" :class="{'active': category === 'files' }">
                <span class="categoryName"><i class="material-icons categoryIcons">insert_drive_file</i>Files</span>
            </a>
            <span class="yellow-bar"></span>
        </div>
    </div>
</template>
<script>

    export default {
        name: 'CategoryMenu',
        props: ['category'],
        components: {
        },
        data() {
            return {
            }
        },
        methods: {
            readUrl: function (e) {
                this.$emit('readUrl', e);
            },
        }
    }
</script>

<style src="../css/GalleryMenu.css" scoped>

</style>