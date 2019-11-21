<template>
    <div class="sidebar-menu"
    :class="{'sidebar-menu-active': this.hamburgerClicked === true,
     'sidebar-menu-inactive' : this.hamburgerClicked === false}">
        <div v-if="isAdmin" class="sidebar-item sidebar__sectionLabel">
            Admin
        </div>
        <div v-if="isAdmin">
            <div :key="route.path" v-for="route in adminRoutes"
            @click="goToRoute(route.path)" class="sidebar-item"
                :class="{'sidebar--active' : isCurrentRoute(route.path)}">
    <!--                {% if currentRoute == route.route or
                         ((route.subRoute is defined and not null)
                        and currentRoute in route.subRoute) %}sidebar&#45;&#45;active{% endif %}"-->
                <i class="material-icons materialDesignIcons">{{ route.icon }}</i>
                <a @click="goToRoute(route.path)">{{ route.label }}</a>
            </div>
        </div>
        <div v-if="isAdmin" @click="downloadCsv" class="sidebar-item">
            <i class="material-icons materialDesignIcons">supervised_user_circle</i>
            <a>Associate csv dump</a>
        </div>
        <div v-if="isUser" class="sidebar-item sidebar__sectionLabel">
            Associate
        </div>
        <div v-if="isUser">
            <div  :key="route.path" v-for="route in associateRoutes"
             @click="goToRoute(route.path)" class="sidebar-item"
             :class="{'sidebar--active' : isCurrentRoute(route.path)}">
            <i class="material-icons materialDesignIcons">{{ route.icon }}</i>
            <a @click="goToRoute(route.path)">{{ route.label }}</a>
            </div>
        </div>
        <div @click="loggingOut" class="sidebar-itemLast sidebar-item">
            <i class="material-icons materialDesignIcons">exit_to_app</i>
            <a @click="loggingOut">{{ 'Logout' }}</a>
        </div>
        <div class="sidebar-item footer" >
<!--            <div v-if="this.checkConfigurationTermsOfService" class="downloadCSV"
                 onclick="goToRoute('{{ configuration.termsOfServices|downloadUrlParser }}')" >-->
<!--                <a href="{{ configuration.termsOfServices|downloadUrlParser }}">
                        Download Terms of service
                    </a>-->
<!--            </div>-->
            <div class="copyRight">© Copyright Something</div>
        </div>
    </div>
</template>

<script>
import {
    mapActions, mapMutations, mapState, mapGetters,
} from 'vuex';
import adminRoutes from '../../router/Routes/adminRoutes';
import associateRoutes from '../../router/Routes/associateRoutes';

export default {
    name: 'MenuItems',
    props: ['associate'],
    data() {
        return {
            adminRoutes: adminRoutes.adminRoutes,
            associateRoutes: associateRoutes.associateRoutes,
        };
    },
    methods: {
        downloadCsv() {
            const dependencies = {
                logout: this.logout,
                router: this.$router,
            };
            this.downloadCSV(dependencies);
        },
        goToRoute(path) {
            this.setCurrentPath(path);
            this.$router.push({ path });
        },
        isCurrentRoute(path) {
            return this.currentPath === path;
        },
        loggingOut() {
            this.logout();
            this.$router.push({ path: '/login' });
        },
        ...mapMutations('Security', [
            'logout',
        ]),
        ...mapMutations('Sidebar', [
            'setCurrentPath',
        ]),
        ...mapActions('Sidebar', [
            'downloadCSV',
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapState('Sidebar', [
            'configuration',
            'currentPath',
            'hamburgerClicked',
        ]),
        ...mapGetters('Sidebar', [
            'checkConfigurationTermsOfService',
        ]),
        ...mapGetters('Security', [
            'getAssociate',
            'isAdmin',
            'isUser',
        ]),
    },
    created() {
        this.setCurrentPath(this.$router.currentRoute.path);
    },
};
</script>

<style lang="scss" scoped>
    @import '../css/MenuItems.scss';
</style>
