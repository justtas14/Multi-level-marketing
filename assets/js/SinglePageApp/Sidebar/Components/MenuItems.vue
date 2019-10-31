<template>
    <div class="sidebar-menu">
        <div v-if="isAdmin" class="sidebar-item sidebar__sectionLabel">
            Admin
        </div>
        <div v-if="isAdmin" :key="route.path" v-for="route in this.getAdminRoutes" @click="goToRoute(route.path)" class="sidebar-item"
            :class="{'sidebar--active' : isCurrentRoute(route.path)}">
<!--                {% if currentRoute == route.route or ((route.subRoute is defined and not null) and currentRoute in route.subRoute) %}sidebar&#45;&#45;active{% endif %}"-->

            <i class="material-icons materialDesignIcons">{{ route.icon }}</i>
            <a href="#">{{ route.label }}</a>
        </div>
        <div v-if="isUser" class="sidebar-item sidebar__sectionLabel">
            Associate
        </div>
        <div v-if="isUser" :key="route.path" v-for="route in this.getAssociateRoutes" @click="goToRoute(route.path)" class="sidebar-item"
             :class="{'sidebar--active' : isCurrentRoute(route.path)}">
            <i class="material-icons materialDesignIcons">{{ route.icon }}</i>
            <a href="#">{{ route.label }}</a>
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

    export default {
        name: "MenuItems",
        props: ['associate'],
        data() {
            return {
                currentPath: this.$router.currentRoute.path
            }
        },
        methods: {
            goToRoute: function(path) {
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
        },
        computed: {
            ...mapState('Sidebar', [
                'configuration'
            ]),
            ...mapGetters('Sidebar', [
                'getAdminRoutes',
                'getAssociateRoutes',
                'checkConfigurationTermsOfService'
            ]),
            ...mapGetters('Security', [
                'getAssociate',
                'isAdmin',
                'isUser'
            ]),
        },
        created() {
        }
    }
</script>

<style scoped>
</style>