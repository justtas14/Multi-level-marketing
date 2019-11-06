<template>
    <div class="sidebar">
        <div class="fixed">
            <Logo v-bind:configuration="this.configuration"/>
            <Profile v-bind:associate="this.getAssociate"/>
        </div>
        <MenuItems v-bind:associate="this.getAssociate"/>
    </div>

</template>

<script>
    import '../css/Sidebar.scss'
    import Logo from "./Logo";
    import Profile from "./Profile";
    import MenuItems from "./MenuItems";
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';

    export default {
        name: "Sidebar",
        components: {
            Logo,
            Profile,
            MenuItems
        },
        props: [],
        data() {
            return {

            }
        },
        methods: {

        },
        computed: {
            ...mapState('Sidebar', [
                'configuration'
            ]),
            ...mapGetters('Security', [
                'getAssociate'
            ]),
        },
        created() {

        }
    }
</script>

<style scoped>

</style>