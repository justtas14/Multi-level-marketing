import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../Store';
import Login from "../Pages/Login/Components/Login";
import AdminHome from '../Pages/Admin/Home/Home';
import AssociateHome from '../Pages/Associate/Home/Home';
import Invite from "../Pages/Associate/Invite/Invite";

Vue.use(VueRouter);

const routes = [
    { path: '/login', component: Login },
    { path: '/associate', component: AssociateHome, meta: { requiresAuth: true } },
    { path: '/associate/invite', component: Invite, meta: { requiresAuth: true } },
    { path: '/admin', component: AdminHome, meta: { requiresAuth: true }}
    // { path: "*", redirect: "/home" }
];


let router = new VueRouter({
    mode: 'history',
    routes,
    base: '/'
});

router.beforeEach((to, from, next) => {
    if (to.path === '/') {
        if (!store.getters['Security/isAuthenticated']) {
            console.log('going to login');
            next({
                path: "/login"
            });
        } else if (store.getters['Security/isAdmin']) {
            next({
                path: "/admin"
            });
        } else {
            next({
                path: "/associate"
            });
        }
    }
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters['Security/isAuthenticated']) {
            next();
        } else {
            console.log('going to login');
            next({
                path: "/login",
                query: { redirect: to.fullPath }
            });
        }
    } else {
        next();
    }
});

export default router;
