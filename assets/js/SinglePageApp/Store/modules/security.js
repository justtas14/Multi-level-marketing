import axios from 'axios';
import SecurityAPI from "../api/SecurityApi/security";

const AUTHENTICATING = "AUTHENTICATING",
    AUTHENTICATING_SUCCESS = "AUTHENTICATING_SUCCESS",
    AUTHENTICATING_ERROR = "AUTHENTICATING_ERROR";

const state = {
    isLoading: false,
    error: null,
    isAuthenticated: false,
    token: null,
    user: null
};

const getters = {
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
    }
};

const mutations = {
    [AUTHENTICATING](state) {
        state.isLoading = true;
        state.error = null;
        state.isAuthenticated = false;
        state.user = null;
    },
    [AUTHENTICATING_SUCCESS](state, user) {
        state.isLoading = false;
        state.error = null;
        state.isAuthenticated = true;
        state.user = user;
    },
    [AUTHENTICATING_ERROR](state, error) {
        state.isLoading = false;
        state.error = error;
        state.isAuthenticated = false;
        state.user = null;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};