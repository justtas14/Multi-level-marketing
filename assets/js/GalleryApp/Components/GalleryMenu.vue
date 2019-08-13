<template>
    <div class="categoryMenu">
        <div class="gallery-title">
            <div class="card-title">File gallery</div>
            <input class="file-upload-input" id="files" type='file'  @change="readUrl"/>
            <label for="files"><i class="fa fa-plus" aria-hidden="true"></i></label>

        </div>
        <div id="material-tabs">
            <a id="tab1-tab" @click="$emit('categorise-files', 'all')"  :class="{'active': category === 'all' }">
                <i class="far fa-file-archive categoryIcons"></i>
                <span class="categoryName">All files</span>
            </a>
            <a id="tab2-tab" @click="$emit('categorise-files', 'images')" :class="{'active': category === 'images' }">
                <i class="far fa-image categoryIcons"></i>
                <span class="categoryName" style="margin-left: 14px">Images</span>
            </a>
            <a id="tab3-tab" @click="$emit('categorise-files', 'files')" :class="{'active': category === 'files' }">
                <i class="far fa-file categoryIcons"></i>
                <span class="categoryName">Files</span>
            </a>
            <span class="yellow-bar"></span>
        </div>
<!--        <input class="file-upload-input" id="files" type='file'  @change="readUrl"/>-->
<!--        <label for="files" class="waves-effect waves-light btn addFileBtn">Add file</label>-->
    </div>
    <!--        <div class="dropdown">-->
<!--            <div class="category-content">-->
<!--                <a @click="$emit('categorise-files', 'all')" v-bind:class="{'category-content-a-active': category === 'all' }">-->
<!--                    <i class="far fa-file-archive categoryIcons"></i>-->
<!--                    <div class="categoryName">All files</div>-->
<!--                </a>-->
<!--                <a @click="$emit('categorise-files', 'images')" v-bind:class="{'category-content-a-active': category === 'images' }">-->
<!--                    <i class="far fa-image categoryIcons"></i>-->
<!--                    <div class="categoryName" style="margin-left: 14px">Images</div>-->
<!--                </a>-->
<!--                <a @click="$emit('categorise-files', 'files')" v-bind:class="{'category-content-a-active': category === 'files' }">-->
<!--                    <i class="far fa-file categoryIcons"></i>-->
<!--                    <div class="categoryName">Files</div>-->
<!--                </a>-->
<!--            </div>-->
<!--        </div>-->
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