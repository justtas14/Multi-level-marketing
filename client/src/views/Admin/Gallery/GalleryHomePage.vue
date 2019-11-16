<template>
    <div class="admin-contentContainer" style="overflow:hidden">
        <div class="card">
            <div class="card-content">
                <section id="#gallery">
                    <GalleryWrapper></GalleryWrapper>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
// import {
//     mapActions, mapState, mapGetters, mapMutations,
// } from 'vuex';
import GalleryWrapper from '../../../components/Gallery/GalleryWrapper.vue';
import EventBus from '../../../components/Gallery/EventBus/EventBus';
import Parameters from '../../../../parameters';

export default {
    name: 'GalleryHomePage',
    components: {
        GalleryWrapper,
    },
    props: [],
    data() {
        return {

        };
    },
    methods: {
    },
    mounted() {
        EventBus.$on('oneClickFile', (fileId, fileName, filePath, downloadPath) => {
            const paramObj = new Parameters();
            window.location.href = `${paramObj.getApiHostUrl(process.env.VUE_APP_HOST_URL)}${downloadPath}`;
        });
    },
    computed: {
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/GalleryHomePage.scss';
</style>
