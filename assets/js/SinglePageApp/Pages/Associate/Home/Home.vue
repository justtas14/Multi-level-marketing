<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <div class="titleContainer">
                    <span class="card-title">My Team</span>
                    <div class="associate-buttonContainer">
                        <a
                            @click="goToRoute('/associate/invite')"
                            id="inviteAssociateBtn"
                            class="btn"
                        >
                            Invite Associate
                        </a>
                        <a
                            @click="goToRoute('/associate/viewer')"
                            id="teamViewerBtn"
                            class="btn"
                        >
                            My Team Viewer
                        </a>
                    </div>
                </div>
                <div v-if="isLoading" class="Spinner__Container user__search__spiner" v-bind:style="{top: 0, 'z-index': 9999}">
                    <div class="lds-dual-ring"/>
                </div>
                <BusinessShape
                    v-else
                    v-bind:associatesInLevels="this.getAssociateInLevels"
                    v-bind:levels="this.getLevels"
                    v-bind:maxLevel="this.getMaxLevel"
                ></BusinessShape>
            </div>
        </div>

        <div class="card" v-if="parent">
            <div class="card-content">
                <span class="card-title">My Enroller</span>
                <div style="display: flex; justify-content: center; flex-direction: row; align-items: center">
                    <div class="associate-enrollerPictureContainer sidebarProfile__pictureContainer">
                        <img v-if="parent.filePath" class="associate-enrollerPicture sidebar-picture" :src="parent.filePath" />
                        <img v-else class="associate-enrollerPicture sidebar-picture" :src="profilePicture" />
                    </div>
                    <div class="associate-enrollerDetailsContainer" style="padding-left:0.5em">
                        <p><b>Email</b>: {{ parent.email }}</p>
                        <p><b>Full name</b>: {{ parent.fullName }}</p>
                        <p><b>Mobile phone</b>:
                            {{ parent.mobilePhone }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <span class="card-title">My Direct Downline</span>
                <table class="mobileTable">
                    <thead>
                        <tr>
                            <th class="associate-overflow">Name</th>
                            <th class="associate-overflow">Email</th>
                            <th class="associate-overflow" >Mobile Phone</th>
                            <th class="associate-overflow">Join Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="getDirectAssociates" v-for="directAssociate in getDirectAssociates" class="associate-container">
                            <td class="associate-overflow directAssociatesAbout">{{ directAssociate.fullName }}</td>
                            <td class="associate-overflow directAssociatesAbout">{{ directAssociate.email }}</td>
                            <td class="associate-overflow directAssociatesAbout">
                                <span v-if="directAssociate.mobilePhone">{{ directAssociate.mobilePhone }}</span>
                                <span v-else>-</span>
                            </td>
                            <td class="associate-overflow directAssociatesAbout">
                                {{ formatDate(directAssociate.joinDate) }}
                            </td>
                        </tr>
                        <tr v-else><td colspan="4">You do not have any associates in your direct downline</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import BusinessShape from "../../../Components/BusinessShape/BusinessShape";
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';
    import profilePicture from '../../../../../../public/assets/images/profile.jpg'
    import '../../../CommonCss/mobileTable.scss';
    import './css/Home.scss'

    export default {
        name: "AssociateHome",
        components: {
            BusinessShape
        },
        props: [],
        data() {
            return {
                profilePicture: profilePicture
            }
        },
        methods: {
            formatDate(date) {
                const d = new Date(date);
                return ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + d.getFullYear();
            },
            goToRoute(path) {
                this.setCurrentPath(path);
                this.$router.push({path: path});
            },
            ...mapActions('AssociateHome', [
                'associateHomeApi'
            ]),
            ...mapMutations('Sidebar', [
                'setCurrentPath'
            ]),
        },
        mounted() {
        },
        computed: {

            ...mapState('AssociateHome', [
                'parent',
                'isLoading'
            ]),
            ...mapGetters('Security', [
                'getAssociate',
            ]),
            ...mapGetters('AssociateHome', [
                'getAssociateInLevels',
                'getDirectAssociates',
                'getLevels',
                'getMaxLevel'
            ]),
        },
        async created() {
            await this.associateHomeApi();
        }
    }
</script>

<style scoped>

</style>