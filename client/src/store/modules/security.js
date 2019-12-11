import axios from 'axios';
import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    isLoading: false,
    isLoggedIn: false,
    error: null,
    associate: null,
    token: null,
    isAuthenticated: false,
};

const getters = {
    isAuthenticated(state) {
        return state.isAuthenticated;
    },
    getToken(state) {
        return state.token;
    },
    getAssociate(state) {
        return state.associate;
    },
    hasError(state) {
        return state.error !== null;
    },
    isAdmin(state) {
        return state.associate.roles.includes('ROLE_ADMIN');
    },
    isUser(state) {
        return state.associate.roles.includes('ROLE_USER');
    },
};

const actions = {
    async login({ commit }, payload) {
        commit('authenticating');
        try {
            const response = await axios.post('/api/token', {
                email: payload.login,
                password: payload.password,
            });
            commit('authenticatingSuccess', { response });
            return response.data;
        } catch (response) {
            const error = response.response.data['#'][0];
            commit('authenticatingError', error);
            return null;
        }
    },
    async setCookie({ state }) {
        const securityApiObj = new SecurityAPI();
        const res = await securityApiObj.setCookie(state.token);
        console.log('cookieResponse', res);
    },

    async unsetCookie({ state }) {
        const securityApiObj = new SecurityAPI();
        const res = await securityApiObj.unsetCookie(state.token);
        console.log(res);
    },

    async loadAssociate({ commit, state }) {
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.authenticateMe(state.token);
        commit('providingDataOnAuth', response.data);
    },
};

const mutations = {
    authenticating(state) {
        state.isLoading = true;
        state.error = null;
        state.associate = null;
        state.isAuthenticated = false;
    },
    authenticatingSuccess(state, token) {
        state.isLoggedIn = true;
        state.isLoading = false;
        state.error = null;
        state.token = token;
        state.isAuthenticated = true;
    },
    authenticatingError(state, error) {
        state.isLoading = false;
        state.error = error;
        state.isAuthenticated = false;
        state.associate = null;
    },
    providingDataOnAuth(state, payload) {
        state.isLoggedIn = false;
        state.isLoading = false;
        state.error = null;
        state.associate = payload.associate;
    },
    logout(state) {
        state.isAuthenticated = false;
        state.token = null;
        state.associate = null;
        state.isLoggedIn = false;
    },
    setAssociate(state, associate) {
        state.associate = associate;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
