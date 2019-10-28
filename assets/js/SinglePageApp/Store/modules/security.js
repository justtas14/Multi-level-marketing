import axios from 'axios';
import SecurityAPI from "../api/SecurityApi/security";

const AUTHENTICATING = "AUTHENTICATING",
    AUTHENTICATING_SUCCESS = "AUTHENTICATING_SUCCESS",
    AUTHENTICATING_ERROR = "AUTHENTICATING_ERROR",
    PROVIDING_DATA_ON_AUTHENTICATION = "PROVIDING_DATA_ON_AUTHENTICATION";

const state = {
    isLoading: false,
    error: null,
    isAuthenticated: false,
    token: null,
    user: null
};

const getters = {
    isAuthenticated({state, commit}) {
        return state.isAuthenticated;
    },
    hasError(state) {
        return state.error !== null;
    },
    hasRole(state) {
        return role => {
            return state.user.roles.indexOf(role) !== -1;
        }
    }
};

const actions = {
    async login({commit}, payload) {
        commit(AUTHENTICATING);
        try {
            let response = await SecurityAPI.login(payload.login, payload.password);
            commit(AUTHENTICATING_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(AUTHENTICATING_ERROR, error);
            return null;
        }
    },
    async authentication({commit, state}) {
        if (!state.token) {
            state.isAuthenticated = false;
        } else {
            let response = await SecurityAPI.authenticate(state.token);
            commit(PROVIDING_DATA_ON_AUTHENTICATION, response.payload);
        }
    },
};

const mutations = {
    [AUTHENTICATING](state) {
        state.isLoading = true;
        state.error = null;
        state.isAuthenticated = false;
        state.user = null;
    },
    [AUTHENTICATING_SUCCESS](state, data) {
        state.isLoading = false;
        state.error = null;
        state.isAuthenticated = true;
        state.token = data.token;
    },
    [AUTHENTICATING_ERROR](state, error) {
        state.isLoading = false;
        state.error = error;
        state.isAuthenticated = false;
        state.user = null;
    },
    [PROVIDING_DATA_ON_AUTHENTICATION](state, payload) {
        state.isLoading = false;
        state.error = null;
        state.isAuthenticated = payload.isAuthenticated;
        state.user = payload.user;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};