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
                <BusinessShape
                    v-else
                    v-bind:associatesInLevels="this.getAssociateInLevels"
                    v-bind:levels="this.getLevels"
                    v-bind:maxLevel="this.getMaxLevel"
                ></BusinessShape>
            </div>
        </div>

        <div class="card" v-if="this.getAssociate.parent">
            <div class="card-content">
                <span class="card-title">My Enroller</span>
                <div style="display: flex; justify-content: center; flex-direction: row; align-items: center">
                    <div class="associate-enrollerPictureContainer sidebarProfile__pictureContainer">
                        <img v-if="this.getAssociate.parent.filePath" class="associate-enrollerPicture sidebar-picture" :src="this.getAssociate.parent.filePath" />
                        <img v-else class="associate-enrollerPicture sidebar-picture" :src="profilePicture" />
                    </div>
                    <div class="associate-enrollerDetailsContainer" style="padding-left:0.5em">
                        <p><b>Email</b>: {{ parent.email }}</p>
                        <p><b>Full name</b>: {{ parent.fullName }}</p>
                        <p><b>Mobile phone</b>:
                            {% if parent.mobilePhone == '' %}
                            -
                            {% else %}
                            {{ parent.mobilePhone }}
                            {% endif %}
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
                    {% if directAssociates is empty %}
                    <tr><td colspan="4">You do not have any associates in your direct downline</td></tr>
                    {% endif %}
                    {% for associate in directAssociates %}
                    <tr class="associate-container">
                        <td class="associate-overflow directAssociatesAbout">{{ associate.fullName }}</td>
                        <td class="associate-overflow directAssociatesAbout">{{ associate.email }}</td>
                        <td class="associate-overflow directAssociatesAbout">
                            {% if associate.mobilePhone == '' %}
                            -
                            {% else %}
                            {{ associate.mobilePhone }}
                            {% endif %}
                        </td>
                        <td class="associate-overflow directAssociatesAbout">
                            {{ associate.joinDate|date('d-m-Y') }}
                        </td>
                    </tr>
                    {% endfor %}
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


        },
        mounted() {
        },
        computed: {

            ...mapGetters('Security', [
                'getAssociate',
            ]),
        },
        async created() {

        }
    }
</script>

<style scoped>

</style>