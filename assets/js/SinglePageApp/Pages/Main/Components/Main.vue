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
        props: [],
        data() {
            return {

            }
        },
        methods: {

            ...mapActions('Security', [
            ]),
            ...mapMutations('Security', [
            ])
        },
        computed: {
            ...mapGetters('Security', [
                'isAuthenticated'

            ]),
            ...mapState('Security', [
            ])
        },
        created() {



            if (!this.isAuthenticated) {
                this.$router.push({path: "/login"})
            } else {

            }

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