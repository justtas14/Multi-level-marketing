<template>
    <div class="admin-contentContainer" style="overflow:hidden">
        <div class="card">
            <div class="card-content">
                <span class="card-title">System Logs</span>
                <Logging></Logging>
            </div>
        </div>
    </div>
</template>

<script>
// import {
//     mapActions, mapState, mapGetters, mapMutations,
// } from 'vuex';
import Logging from '../../../components/Logging/Logging.vue';

export default {
    name: 'Logs',
    components: {
        Logging,
    },
    props: [],
    data() {
        return {

        };
    },
    methods: {
    },
    mounted() {

    },
    computed: {
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Logs.scss';
</style>
