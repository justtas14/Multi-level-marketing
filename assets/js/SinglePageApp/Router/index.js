import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../Store';
import Login from "../Pages/Login/Components/Login";

Vue.use(VueRouter);

const routes = [
    // { path: '/associate', component: AssociateHome },
    // { path: '/associate/profile', component: AssociateProfile},
    // { path: '/associate/invite', component: AssociateInvitation },
    // { path: '/associate/viewer', component: AssociateTeamViewer},
    { path: '/login', component: Login }
];


let router = new VueRouter({
    mode: 'history',
    routes,
    base: '/'
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters["security/isAuthenticated"]) {
            next();
        } else {
            next({
                path: "/login",
                query: { redirect: to.fullPath }
            });
        }
    } else {
        next(); // make sure to always call next()
    }
});

export default router;
