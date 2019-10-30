import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../Store';
import Login from "../Pages/Login/Components/Login";
import AdminHome from '../Pages/Admin/Home/Home';
import AssociateHome from '../Pages/Associate/Home/Home';

Vue.use(VueRouter);

const routes = [
    { path: '/associate', component: AssociateHome, meta: { requiresAuth: true } },
    { path: '/admin', component: AdminHome, meta: { requiresAuth: true }},
    { path: '/login', component: Login },
    // { path: "*", redirect: "/home" }
];


let router = new VueRouter({
    mode: 'history',
    routes,
    base: '/'
});

router.beforeEach((to, from, next) => {
    if (to.path === '/') {
        if (!localStorage.isAuthenticated) {
            next({
                path: "/login"
            });
        } else if (store.getters('Security/isAdmin')) {
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
        if (localStorage.isAuthenticated) {
            next();
        } else {
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
