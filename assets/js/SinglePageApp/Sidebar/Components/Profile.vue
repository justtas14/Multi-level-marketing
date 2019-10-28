<template>
    <div class="sidebar-profileContainer">
        <a v-if="user.associate" @click="redirectToEditProfile">
            <div class="sidebarProfile__container">
                <div class="sidebarProfile__pictureContainer">
                    <img v-if="user.associate && user.associate.profilePicture" class="sidebar-picture" :src="user.associate.filePath" />
                    <img v-else class="sidebar-picture" :src="defaultPicture"/>
                </div>
                <div class="sidebarProfile__labelContainer">
                    <span v-if="user.associate">
                        {{ associateName(user.associate.fullName, 30) }}
                    </span>
                    <span v-else>
                        {{ associateName(user.email,30) }}
                    </span>
                </div>
            </div>
        </a>
    </div>
</template>

<script>
    import '../css/Profile.scss'
    import defaultPicture from '../../../../../public/assets/images/profile.jpg'

    export default {
        name: "Profile",
        props: ['user'],
        components: {

        },
        data() {
            return {

            }
        },
        methods: {
            redirectToEditProfile: function () {
                this.$router.push({path: "/associate/profile"})
            }
        },
        computed: {
            associateName: function (name, n) {
                return (name.length > n) ? name.substr(0, n-1) + '&hellip;' : name;
            }
        },
        created() {

        }
    }
</script>

<style scoped>

</style>