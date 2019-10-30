import axios from 'axios';
import SecurityAPI from "../api/SecurityApi/security";

const AUTHENTICATING = "AUTHENTICATING",
    AUTHENTICATING_SUCCESS = "AUTHENTICATING_SUCCESS",
    AUTHENTICATING_ERROR = "AUTHENTICATING_ERROR",
    PROVIDING_DATA_ON_AUTHENTICATION = "PROVIDING_DATA_ON_AUTHENTICATION";

const state = {
    isLoading: false,
    error: null
};

const getters = {
    isAuthenticated(state) {
        return localStorage.isAuthenticated;
    },
    hasError(state) {
        return state.error !== null;
    },
    isAdmin(state) {
        console.log(JSON.parse(localStorage.associate));
        return JSON.parse(localStorage.associate).roles.includes("ROLE_ADMIN");
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
            // commit(AUTHENTICATING_ERROR);
            console.log(response);
            console.log(response.message);
            return null;
        }
    },
    async loadAssociate({commit, state}) {
        let response = await SecurityAPI.authenticatePostApi('/api/associate/me', localStorage.token);
        commit(PROVIDING_DATA_ON_AUTHENTICATION, response.data);
        return true;
    },
};

const mutations = {
    [AUTHENTICATING](state) {
        state.isLoading = true;
        state.error = null;
        localStorage.isAuthenticated = false;
        localStorage.associate = null;
    },
    [AUTHENTICATING_SUCCESS](state, data) {
        state.isLoading = false;
        state.error = null;
        localStorage.token = data.token;
        localStorage.isAuthenticated = true;
    },
    [AUTHENTICATING_ERROR](state, error) {
        state.isLoading = false;
        state.error = error;
        localStorage.isAuthenticated = false;
        localStorage.associate = null;
    },
    [PROVIDING_DATA_ON_AUTHENTICATION](state, payload) {
        state.isLoading = false;
        state.error = null;
        localStorage.associate = JSON.stringify(payload.associate);
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};