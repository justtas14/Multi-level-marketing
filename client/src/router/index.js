import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store';
import Login from '../views/Login/Components/Login.vue';
import AdminHome from '../views/Admin/Home/Home.vue';
import AssociateHome from '../views/Associate/Home/Home.vue';
import Invite from '../views/Associate/Invite/Invite.vue';

Vue.use(VueRouter);

const routes = [
    { path: '/login', component: Login },
    { path: '/associate', component: AssociateHome, meta: { requiresAuth: true } },
    { path: '/associate/invite', component: Invite, meta: { requiresAuth: true } },
    { path: '/admin', component: AdminHome, meta: { requiresAuth: true } },
    // { path: "*", redirect: "/" }
];


const router = new VueRouter({
    mode: 'history',
    routes,
    base: '/',
});

router.beforeEach((to, from, next) => {
    if (to.path === '/') {
        if (!store.getters['Security/isAuthenticated']) {
            console.log('going to login');
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
            console.log('going to login');
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
