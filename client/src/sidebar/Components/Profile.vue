<template>
    <div class="sidebar-profileContainer"
    :class="{'sidebar-profileContainer-active': this.hamburgerClicked === true,
     'sidebar-profileContainer-inactive': this.hamburgerClicked === false}">
        <a @click="redirectToEditProfile">
            <div class="sidebarProfile__container">
                <div class="sidebarProfile__pictureContainer">
                    <img v-if="associate && associate.profilePicture"
                    class="sidebar-picture" :src="getPicture()" />
                    <img v-else class="sidebar-picture" :src="defaultPicture"/>
                </div>
                <div class="sidebarProfile__labelContainer">
                    <span v-if="associate.fullName">
                        {{ associateName(associate.fullName, 30) }}
                    </span>
                    <span v-else>
                        {{ associateName(associate.email, 30) }}
                    </span>
                </div>
            </div>
        </a>
    </div>
</template>

<script>
import {
    mapMutations,
    mapState,
} from 'vuex';
import defaultPicture from '../../../public/img/profile.jpg';
import Parameters from '../../../parameters';


export default {
    name: 'Profile',
    props: ['associate'],
    components: {

    },
    data() {
        return {
            defaultPicture,
        };
    },
    methods: {
        getPicture() {
            return `${Parameters.API_HOST_URL}${this.associate.filePath}`;
        },
        redirectToEditProfile() {
            this.$router.push({ path: '/associate/profile' });
        },
        associateName(name, n) {
            return (name.length > n) ? `${name.substr(0, n - 1)}&hellip;` : name;
        },
        ...mapMutations('Sidebar', [
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapState('Sidebar', [
            'hamburgerClicked',
        ]),
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import '../css/Profile.scss';
</style>
