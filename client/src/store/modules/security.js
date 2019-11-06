import axios from 'axios';
import SecurityAPI from "../api/SecurityApi/security";

const AUTHENTICATING = "AUTHENTICATING",
    AUTHENTICATING_SUCCESS = "AUTHENTICATING_SUCCESS",
    AUTHENTICATING_ERROR = "AUTHENTICATING_ERROR",
    PROVIDING_DATA_ON_AUTHENTICATION = "PROVIDING_DATA_ON_AUTHENTICATION";

const state = {
    isLoading: false,
    isLoggedIn: false,
    error: null,
    associate: null,
    token: null,
    isAuthenticated: false
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
        return state.associate.roles.includes("ROLE_ADMIN");
    },
    isUser(state) {
        return state.associate.roles.includes("ROLE_USER");
    }
};

const actions = {
    async login({commit}, payload) {
        commit(AUTHENTICATING);
        try {
            let response = await SecurityAPI.login(payload.login, payload.password);
            commit(AUTHENTICATING_SUCCESS, response.data);
            return response.data;
        } catch (response) {
            commit(AUTHENTICATING_ERROR, response.response.data['#'][0]);
            return null;
        }
    },
    async loadAssociate({commit, state}) {
        let response = await SecurityAPI.authenticateMe(state.token);
        commit(PROVIDING_DATA_ON_AUTHENTICATION, response.data);
        return true;
    },
};

const mutations = {
    [AUTHENTICATING](state) {
        state.isLoading = true;
        state.error = null;
        state.associate = null;
        state.isAuthenticated = false;
    },
    [AUTHENTICATING_SUCCESS](state, data) {
        state.isLoggedIn = true;
        state.isLoading = false;
        state.error = null;
        state.token = data.token;
        state.isAuthenticated = true;
    },
    [AUTHENTICATING_ERROR](state, error) {
        state.isLoading = false;
        state.error = error;
        state.isAuthenticated = false;
        state.associate = null;
    },
    [PROVIDING_DATA_ON_AUTHENTICATION](state, payload) {
        state.isLoggedIn = false;
        state.isLoading = false;
        state.error = null;
        state.associate = payload.associate;
    },
    logout(state) {
        state.isAuthenticated = false;
        state.token = null;
        state.associate = null;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};