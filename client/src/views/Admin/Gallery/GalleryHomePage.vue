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
import {
    mapMutations,
} from 'vuex';
import GalleryWrapper from '../../../components/Gallery/GalleryWrapper.vue';
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

        ...mapMutations('Gallery', [
            'changeClickFile',
        ]),
    },
    mounted() {
        this.changeClickFile((fileId, fileName, fileSrc, filePath) => {
            window.location.href = `${Parameters.API_HOST_URL}${filePath}`;
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
