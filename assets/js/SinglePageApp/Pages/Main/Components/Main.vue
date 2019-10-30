<template>
    <div id="main">
        <Sidebar v-if="localStorage.isAuthenticated"></Sidebar>
        <router-view></router-view>
    </div>
</template>

<script>
    import axios from "axios";
    import '../css/Main.scss';
    import Sidebar from "../../../Sidebar/Components/Sidebar";
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex'

    export default {
        name: "Main",
        components: {
            Sidebar
        },
        props: [],
        data() {
            return {
                localStorage: localStorage
            }
        },
        methods: {

            ...mapActions('Security', [
            ]),
            ...mapActions('Sidebar', [
                'configurationApi'
            ]),
            ...mapMutations('Security', [
            ])
        },
        mounted() {
        },
        computed: {
            ...mapGetters('Security', [
                'isAdmin',
            ]),
            ...mapState('Security', [
            ]),
        },
        async created() {
            await this.configurationApi();

            if (!localStorage.isAuthenticated) {
                this.$router.push({path: "/login"})
            }

            // axios.interceptors.response.use(undefined, (err) => {
            //     return new Promise(() => {
            //         if (err.response.status === 401) {
            //             this.$router.push({path: "/login"})
            //         }
            //         throw err;
            //     });
            // });
        }
    }
</script>

<style scoped>

</style>