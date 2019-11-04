<template>
    <div class="sidebar-menu">
        <div v-if="isAdmin" class="sidebar-item sidebar__sectionLabel">
            Admin
        </div>
        <div v-if="isAdmin" :key="route.path" v-for="route in adminRoutes" @click="goToRoute(route.path)" class="sidebar-item"
            :class="{'sidebar--active' : isCurrentRoute(route.path)}">
<!--                {% if currentRoute == route.route or ((route.subRoute is defined and not null) and currentRoute in route.subRoute) %}sidebar&#45;&#45;active{% endif %}"-->

            <i class="material-icons materialDesignIcons">{{ route.icon }}</i>
            <a @click="goToRoute(route.path)">{{ route.label }}</a>
        </div>
        <div v-if="isAdmin" @click="this.downloadCSV" class="sidebar-item">
            <i class="material-icons materialDesignIcons">supervised_user_circle</i>
            <a @click="">Associate csv dump</a>
        </div>
        <div v-if="isUser" class="sidebar-item sidebar__sectionLabel">
            Associate
        </div>
        <div v-if="isUser" :key="route.path" v-for="route in associateRoutes" @click="goToRoute(route.path)" class="sidebar-item"
             :class="{'sidebar--active' : isCurrentRoute(route.path)}">
            <i class="material-icons materialDesignIcons">{{ route.icon }}</i>
            <a @click="goToRoute(route.path)">{{ route.label }}</a>
        </div>
        <div @click="loggingOut" class="sidebar-itemLast sidebar-item">
            <i class="material-icons materialDesignIcons">exit_to_app</i>
            <a @click="loggingOut">{{ 'Logout' }}</a>
        </div>
        <div class="sidebar-item footer" >
<!--            <div v-if="this.checkConfigurationTermsOfService" class="downloadCSV" onclick="goToRoute('{{ configuration.termsOfServices|downloadUrlParser }}')" >-->
<!--                <a href="{{ configuration.termsOfServices|downloadUrlParser }}">Download Terms of service</a>-->
<!--            </div>-->
            <div class="copyRight">© Copyright Something</div>
        </div>
    </div>
</template>

<script>
    import '../css/MenuItems.scss';
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';
    import adminRoutes from "../../Router/Routes/adminRoutes";
    import associateRoutes from "../../Router/Routes/associateRoutes";

    export default {
        name: "MenuItems",
        props: ['associate'],
        data() {
            return {
                adminRoutes: adminRoutes.adminRoutes,
                associateRoutes: associateRoutes.associateRoutes
            }
        },
        methods: {
            goToRoute: function(path) {
                this.setCurrentPath(path);
                this.$router.push({path: path});
            },
            isCurrentRoute: function (path) {
                return this.currentPath === path;
            },
            loggingOut: function () {
                this.logout();
                this.$router.push({path: '/login'});
            },
            ...mapMutations('Security', [
                'logout'
            ]),
            ...mapMutations('Sidebar', [
                'setCurrentPath'
            ]),
            ...mapActions('Sidebar', [
                'downloadCSV'
            ]),
        },
        computed: {
            ...mapState('Sidebar', [
                'configuration',
                'currentPath'
            ]),
            ...mapGetters('Sidebar', [
                'checkConfigurationTermsOfService'
            ]),
            ...mapGetters('Security', [
                'getAssociate',
                'isAdmin',
                'isUser'
            ]),
        },
        created() {
            this.setCurrentPath(this.$router.currentRoute.path);
        }
    }
</script>

<style scoped>
</style>