<template>
    <div>
        <div id="rootExplorer"
                :data-source="getPath()"
                data-details="">
        </div>
    </div>
</template>

<script>
// import explorer2 from '../../assets/js/explorer_2';
// import explorerRuntimeMain from '../../assets/js/explorer_runtime_main';
// import explorerMain from '../../assets/js/explorer_main';
import './css/explorer_main.css';
import Parameters from '../../../parameters';

// Vue.use(explorer2);
// Vue.use(explorerRuntimeMain);

export default {
    name: 'RootExplorer',
    components: {

    },
    props: [],
    methods: {
        getPath() {
            return `${Parameters.API_HOST_URL}/api/admin/explorer`;
        },
    },
    computed: {

    },
    mounted() {
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
</style>
