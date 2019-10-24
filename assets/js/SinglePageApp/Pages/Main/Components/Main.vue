<template>
    <div id="main">
        <Sidebar v-if="isAuthenticated"></Sidebar>
        <router-view></router-view>
    </div>
</template>

<script>
    import axios from "axios";
    import '../css/Main.scss'
    import Sidebar from "../../../Sidebar/Components/Sidebar";
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex'

    export default {
        name: "Main",
        components: {
            Sidebar
        },
        props: ['isAuthenticatedOnRefresh', 'user'],
        data() {
            return {

            }
        },
        methods: {

            ...mapActions('Security', [
                'onRefresh'
            ]),
            ...mapMutations('Security', [
            ])
        },
        computed: {
            ...mapGetters('Security', [

            ]),
            ...mapState('Security', [
                'isAuthenticated'
            ])
        },
        created() {
            let isAuthenticatedOnRefresh = JSON.parse(this.isAuthenticated);
            let user = JSON.parse(this.user);

            console.log(isAuthenticatedOnRefresh);
            console.log(user);

            let payload = { isAuthenticated: isAuthenticatedOnRefresh, user: user };
            this.onRefresh(payload);

            axios.interceptors.response.use(undefined, (err) => {
                return new Promise(() => {
                    if (err.response.status === 401) {
                        this.$router.push({path: "/login"})
                    }
                    throw err;
                });
            });

        }
    }
</script>

<style scoped>

</style>