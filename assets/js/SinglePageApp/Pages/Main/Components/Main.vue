<template>
    <div id="main">
        <Sidebar v-if="isAuthenticated"></Sidebar>
        <router-view></router-view>
    </div>
</template>

<script>
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

            ...mapActions('security', [
            ]),
            ...mapMutations('security', [
            ])
        },
        computed: {
            ...mapGetters('security', {
            }),
            ...mapState('security', [
                'isAuthenticated'
            ])
        },
        created() {

        }
    }
</script>

<style scoped>

</style>