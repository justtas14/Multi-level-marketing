<template>
    <div class="admin-contentContainer" style="overflow:hidden">
        <div class="card">
            <div class="card-content">
                <span class="card-title">User Search</span>
                <Main v-bind:mainAction="this.mainAction"
                 v-bind:mainActionLabel="'Details'"></Main>
            </div>
        </div>
    </div>
</template>

<script>
// import {
//     mapState,
// } from 'vuex';
import Main from '../../../components/UserSearch/Main.vue';

export default {
    name: 'UserSearchHome',
    components: {
        Main,
    },
    props: [],
    data() {
        return {
            mainAction: {
                fun: (id) => {
                    this.$router.push(`/admin/user/${id}`);
                },
                confirm: false,
            },
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
    @import './css/UserSearchHome.scss';
</style>
