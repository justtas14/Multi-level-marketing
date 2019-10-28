<template>
    <div class="sidebar-logoContainer">
        <img v-if="configuration.mainLogo" class="sidebar-logo" :src="configuration.mainLogoPath" />
        <img v-else class="sidebar-logo" :src="PlumTreeSystemsLogo" />
        <div class="hamburger hamburger--collapse">
            <div class="hamburger-box">
                <div class="hamburger-inner"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import '../css/Logo.scss'
    import PlumTreeSystemsLogo from '../../../../../public/assets/images/plum_tree_logo.png'

    export default {
        name: "Logo",
        components: {

        },
        props: ['configuration'],
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

<style>

</style>