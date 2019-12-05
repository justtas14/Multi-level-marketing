<template>
    <div class="admin-contentContainer" style="overflow:hidden">
        <div class="card">
            <div class="card-content">
                <span class="card-title">User Search</span>
                <Success v-if="this.formSuccess.type === 'delete' && $route.query.userDeleted"
              v-bind:message="this.formSuccess.message"></Success>
                <Main v-bind:mainAction="this.mainAction"
                 v-bind:mainActionLabel="'Details'"></Main>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapState, mapMutations,
} from 'vuex';
import Main from '../../../components/UserSearch/Main.vue';
import Success from '../../../components/Messages/Success.vue';

export default {
    name: 'UserSearchHome',
    components: {
        Main,
        Success,
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

        ...mapMutations('Sidebar', [
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapState('AssociateDetails', [
            'formSuccess',
        ]),
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/UserSearchHome.scss';
</style>
