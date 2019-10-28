<template>
    <div class="sidebar">
        <div class="fixed">
            <Logo/>
            <Profile/>
        </div>
        <MenuItems/>
    </div>

</template>

<script>
    import '../css/Sidebar.scss'
    import Logo from "./Logo";
    import Profile from "./Profile";
    import MenuItems from "./MenuItems";

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

        },
        created() {

        }
    }
</script>

<style scoped>

</style>