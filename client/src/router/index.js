import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store/index';
// import Login from '../views/Login/Components/Login.vue';
import AdminHome from '../views/Admin/Home/Home.vue';
import AssociateHome from '../views/Associate/Home/Home.vue';
import Invite from '../views/Associate/Invite/Invite.vue';
import GalleryHomePage from '../views/Admin/Gallery/GalleryHomePage.vue';
import Logs from '../views/Admin/Logs/Logs.vue';
import UserSearchHome from '../views/Admin/UserSearch/UserSearchHome.vue';
import Profile from '../views/Associate/Profile/Profile.vue';
import AssociateDetailsHome from '../views/Admin/AssociateDetails/AssociateDetailsHome.vue';
import EndPrelaunch from '../views/Admin/EndPrelaunch/EndPrelaunch.vue';
import ChangeContent from '../views/Admin/ChangeContent/ChangeContent.vue';
import EmailTemplate from '../views/Admin/EmailTemplates/EmailTemplate.vue';
import EmailtemplatesList from '../views/Admin/EmailTemplates/EmailTemplatesList.vue';

Vue.use(VueRouter);

const routes = [
    // { path: '/login', component: Login },
    { path: '/associate', component: AssociateHome, meta: { requiresAuth: true, associate: true } },
    { path: '/associate/invite', component: Invite, meta: { requiresAuth: true, associate: true } },
    { path: '/associate/profile', component: Profile, meta: { requiresAuth: true, associate: true } },
    { path: '/admin', component: AdminHome, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/gallery', component: GalleryHomePage, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/logs', component: Logs, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/users', component: UserSearchHome, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/user/:id', component: AssociateDetailsHome, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/endprelaunch', component: EndPrelaunch, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/changecontent', component: ChangeContent, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/emailtemplates', component: EmailtemplatesList, meta: { requiresAuth: true, admin: true } },
    { path: '/admin/emailtemplate/:type', component: EmailTemplate, meta: { requiresAuth: true, admin: true } },

    // { path: '*', redirect: '/' },
];


const router = new VueRouter({
    mode: 'history',
    routes,
    saveScrollPosition: true,
    base: '/',
});

router.beforeEach((to, from, next) => {
    if (to.path === '/' && store.getters['Security/isAuthenticated']) {
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
});

export default router;
