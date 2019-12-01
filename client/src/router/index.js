import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store/index';
import Login from '../views/Login/Components/Login.vue';
import AdminHome from '../views/Admin/Home/Home.vue';
import AssociateHome from '../views/Associate/Home/Home.vue';
import Invite from '../views/Associate/Invite/Invite.vue';
import GalleryHomePage from '../views/Admin/Gallery/GalleryHomePage.vue';
import Logs from '../views/Admin/Logs/Logs.vue';
import UserSearchHome from '../views/Admin/UserSearch/UserSearchHome.vue';
import Profile from '../views/Associate/Profile/Profile.vue';
import AssociateDetailsHome from '../views/Admin/AssociateDetails/AssociateDetailsHome.vue';
import EndPrelaunch from '../views/Admin/EndPrelaunch/EndPrelaunch.vue';

Vue.use(VueRouter);

const routes = [
    { path: '/login', component: Login },
    { path: '/associate', component: AssociateHome, meta: { requiresAuth: true } },
    { path: '/associate/invite', component: Invite, meta: { requiresAuth: true } },
    { path: '/associate/profile', component: Profile, meta: { requiresAuth: true } },
    { path: '/admin', component: AdminHome, meta: { requiresAuth: true } },
    { path: '/admin/gallery', component: GalleryHomePage, meta: { requiresAuth: true } },
    { path: '/admin/logs', component: Logs, meta: { requiresAuth: true } },
    { path: '/admin/users', component: UserSearchHome, meta: { requiresAuth: true } },
    { path: '/admin/user/:id', component: AssociateDetailsHome, meta: { requiresAuth: true } },
    { path: '/admin/endprelaunch', component: EndPrelaunch, meta: { requiresAuth: true } },
    { path: '*', redirect: '/' },
];


const router = new VueRouter({
    mode: 'history',
    routes,
    saveScrollPosition: true,
    base: '/',
});

router.beforeEach((to, from, next) => {
    if (to.path === '/' && to.path !== '/login') {
        if (!store.getters['Security/isAuthenticated']) {
            next({
                path: '/login',
            });
        } else if (store.getters['Security/isAdmin']) {
            next({
                path: '/admin',
            });
        } else {
            next({
                path: '/associate',
            });
        }
    }
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters['Security/isAuthenticated']) {
            next();
        } else {
            next({
                path: '/login',
                query: { redirect: to.fullPath },
            });
        }
    } else {
        next();
    }
});

export default router;
