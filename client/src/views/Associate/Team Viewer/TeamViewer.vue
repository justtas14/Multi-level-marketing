<template>
    <div class="admin-contentContainer">
        <NodeExplorer></NodeExplorer>
    </div>
</template>

<script>
import NodeExplorer from '../../../components/NodeExplorer/Main.vue';

export default {
    name: 'TeamViewer',
    props: [],
    data() {
        return {
        };
    },
    components: {
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
