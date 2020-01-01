import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store/index';
import AdminHome from '../views/Admin/Home/Home.vue';
import AssociateHome from '../views/Associate/Home/Home.vue';
import Invite from '../views/Associate/Invite/Invite.vue';
import GalleryHomePage from '../views/Admin/Gallery/GalleryHomePage.vue';
import Logs from '../views/Admin/Logs/Logs.vue';
import UserSearchHome from '../views/Admin/UserSearch/UserSearchHome.vue';
import Profile from '../views/Associate/Profile/Profile.vue';
import TeamViewer from '../views/Associate/Team Viewer/TeamViewer.vue';
import AssociateDetailsHome from '../views/Admin/AssociateDetails/AssociateDetailsHome.vue';
import EndPrelaunch from '../views/Admin/EndPrelaunch/EndPrelaunch.vue';
import ChangeContent from '../views/Admin/ChangeContent/ChangeContent.vue';
import EmailTemplate from '../views/Admin/EmailTemplates/EmailTemplate.vue';
import EmailtemplatesList from '../views/Admin/EmailTemplates/EmailTemplatesList.vue';
import PageNotFound from '../views/PageNotFound/PageNotFound.vue';
import App from '../App.vue';

Vue.use(VueRouter);

const routes = [
    { path: '/', component: App },
    { path: '/associate', component: AssociateHome, meta: { requiresAuth: true, associate: true, title: 'Associate' } },
    { path: '/associate/invite', component: Invite, meta: { requiresAuth: true, associate: true, title: 'Invite' } },
    { path: '/associate/profile', component: Profile, meta: { requiresAuth: true, associate: true, title: 'Profile' } },
    { path: '/associate/viewer', component: TeamViewer, meta: { requiresAuth: true, associate: true, title: 'Team Viewer' } },
    { path: '/admin', component: AdminHome, meta: { requiresAuth: true, admin: true, title: 'Admin' } },
    { path: '/admin/gallery', component: GalleryHomePage, meta: { requiresAuth: true, admin: true, title: 'Gallery' } },
    { path: '/admin/logs', component: Logs, meta: { requiresAuth: true, admin: true, title: 'Logs' } },
    { path: '/admin/users', component: UserSearchHome, meta: { requiresAuth: true, admin: true, title: 'Users' } },
    { path: '/admin/user/:id', component: AssociateDetailsHome, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/endprelaunch', component: EndPrelaunch, meta: { requiresAuth: true, admin: true, title: 'End Prelaunch' } },
    { path: '/admin/changecontent', component: ChangeContent, meta: { requiresAuth: true, admin: true, title: 'Change Content' } },
    { path: '/admin/emailtemplates', component: EmailtemplatesList, meta: { requiresAuth: true, admin: true, title: 'Email Templates' } },
    { path: '/admin/emailtemplate/:type', component: EmailTemplate, meta: { requiresAuth: true, admin: true } },
    { path: '*', component: PageNotFound, meta: { requiresAuth: true, title: 'Page Not Found' } },
];


const router = new VueRouter({
    mode: 'history',
    routes,
    saveScrollPosition: true,
    base: '/',
});

router.beforeEach((to, from, next) => {
    if (to.meta.title) {
        document.title = to.meta.title;
    }
    if (to.path === '/' && store.getters['Security/isAuthenticated'] && !store.getters['Security/isLogouting']) {
        if (store.getters['Security/isAdmin']) {
            next({
                path: '/admin',
            });
        } else {
            next({
                path: '/associate',
            });
        }
    }
    if (to.matched.some(record => record.meta.associate)) {
        if (store.getters['Security/isUser']) {
            next();
        } else {
            next({
                path: '/admin',
            });
        }
    }
    if (to.matched.some(record => record.meta.admin)) {
        if (store.getters['Security/isAdmin']) {
            next();
        } else {
            next({
                path: '/associate',
            });
        }
    }
    next();
});

export default router;
