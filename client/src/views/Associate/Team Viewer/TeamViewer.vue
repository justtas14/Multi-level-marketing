<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Team Viewer</span>
                <rootExplorer v-bind:path="'/api/associate/downline'"></rootExplorer>
            </div>
        </div>
    </div>
</template>

<script>
import rootExplorer from '../../../components/RootExplorer/rootExplorer.vue';


export default {
    name: 'TeamViewer',
    props: [],
    data() {
        return {
        };
    },
    components: {
        rootExplorer,
    },
    methods: {
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/TeamViewer.scss';
</style>
