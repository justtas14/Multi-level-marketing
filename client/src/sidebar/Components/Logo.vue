<template>
    <div class="sidebar-logoContainer">
        <img v-if="this.checkConfigurationMainLogo"
         class="sidebar-logo" :src="configuration.mainLogoPath" />
        <img v-else class="sidebar-logo" :src="PlumTreeSystemsLogo" />
        <div @click="toggleMenu" class="hamburger hamburger--collapse"
         :class="{'is-active': this.hamburgerClicked}">
            <div class="hamburger-box">
                <div class="hamburger-inner"></div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapGetters,
    mapMutations,
    mapState,
} from 'vuex';
import PlumTreeSystemsLogo from '../../../public/img/plum_tree_logo.png';

export default {
    name: 'Logo',
    components: {

    },
    props: ['configuration'],
    data() {
        return {
            PlumTreeSystemsLogo,
        };
    },
    methods: {
        toggleMenu() {
            if (this.hamburgerClicked === null) {
                this.setHamburgerClicked(true);
            } else {
                this.setHamburgerClicked(!this.hamburgerClicked);
            }
        },

        ...mapMutations('Sidebar', [
            'setHamburgerClicked',
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapGetters('Sidebar', [
            'checkConfigurationMainLogo',
        ]),
        ...mapState('Sidebar', [
            'hamburgerClicked',
        ]),
    },
    created() {

    },
};
</script>

<style lang="scss" scoped>
    @import '../css/Logo.scss';
</style>
