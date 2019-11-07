<template>
    <div class="sidebar-profileContainer">
        <a @click="redirectToEditProfile">
            <div class="sidebarProfile__container">
                <div class="sidebarProfile__pictureContainer">
                    <img v-if="associate && associate.profilePicture"
                    class="sidebar-picture" :src="associate.filePath" />
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
import '../css/Profile.scss';
import defaultPicture from '../../../public/img/profile.jpg';

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
        redirectToEditProfile() {
            this.$router.push({ path: '/associate/profile' });
        },
        associateName(name, n) {
            return (name.length > n) ? `${name.substr(0, n - 1)}&hellip;` : name;
        },
    },
    computed: {

    },
    created() {
    },
};
</script>

<style scoped>

</style>
