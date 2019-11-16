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

import {
    mapState, mapGetters,
} from 'vuex';
import Logo from './Logo.vue';
import Profile from './Profile.vue';
import MenuItems from './MenuItems.vue';

export default {
    name: 'Sidebar',
    components: {
        Logo,
        Profile,
        MenuItems,
    },
    props: [],
    data() {
        return {

        };
    },
    methods: {

    },
    computed: {
        ...mapState('Sidebar', [
            'configuration',
        ]),
        ...mapGetters('Security', [
            'getAssociate',
        ]),
    },
    created() {

    },
};
</script>

<style lang="scss" scoped>
    @import '../css/Sidebar.scss';
</style>
