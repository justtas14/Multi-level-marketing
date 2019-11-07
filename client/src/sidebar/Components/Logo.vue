<template>
    <div class="sidebar-logoContainer">
        <img v-if="this.checkConfigurationMainLogo"
         class="sidebar-logo" :src="configuration.mainLogoPath" />
        <img v-else class="sidebar-logo" :src="PlumTreeSystemsLogo" />
        <div class="hamburger hamburger--collapse">
            <div class="hamburger-box">
                <div class="hamburger-inner"></div>
            </div>
        </div>
    </div>
</template>

<script>
import '../css/Logo.scss';
import {
    mapGetters,
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
    },
    computed: {
        ...mapGetters('Sidebar', [
            'checkConfigurationMainLogo',
        ]),
    },
    created() {

    },
};
</script>

<style>

</style>
