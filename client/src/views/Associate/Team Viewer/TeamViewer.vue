<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Team Viewer</span>
                <rootExplorer v-bind:path="'/api/associate/downline'"></rootExplorer>
            </div>
        </div>
        <NodeExplorer></NodeExplorer>
    </div>
</template>

<script>
import rootExplorer from '../../../components/RootExplorer/rootExplorer.vue';
import NodeExplorer from '../../../components/NodeExplorer/Main.vue';

export default {
    name: 'TeamViewer',
    props: [],
    data() {
        return {
        };
    },
    components: {
        rootExplorer,
        NodeExplorer,
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
