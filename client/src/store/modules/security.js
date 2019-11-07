import SecurityAPI from '../api/SecurityApi/apiCalls';

const AUTHENTICATING = 'AUTHENTICATING';
const AUTHENTICATING_SUCCESS = 'AUTHENTICATING_SUCCESS';
const AUTHENTICATING_ERROR = 'AUTHENTICATING_ERROR';
const PROVIDING_DATA_ON_AUTHENTICATION = 'PROVIDING_DATA_ON_AUTHENTICATION';

const state = {
    isLoading: false,
    isLoggedIn: false,
    error: null,
    associate: null,
    token: null,
    isAuthenticated: false,
};

const getters = {
    isAuthenticated() {
        return state.isAuthenticated;
    },
    getToken() {
        return state.token;
    },
    getAssociate() {
        return state.associate;
    },
    hasError() {
        return state.error !== null;
    },
    isAdmin() {
        return state.associate.roles.includes('ROLE_ADMIN');
    },
    isUser() {
        return state.associate.roles.includes('ROLE_USER');
    },
};

const actions = {
    async login({ commit }, payload) {
        commit(AUTHENTICATING);
        try {
            const response = await SecurityAPI.login(payload.login, payload.password);
            commit(AUTHENTICATING_SUCCESS, response.data);
            return response.data;
        } catch (response) {
            commit(AUTHENTICATING_ERROR, response.response.data['#'][0]);
            return null;
        }
    },
    async loadAssociate({ commit }) {
        const response = await SecurityAPI.authenticateMe(state.token);
        commit(PROVIDING_DATA_ON_AUTHENTICATION, response.data);
        return true;
    },
};

const mutations = {
    [AUTHENTICATING]() {
        state.isLoading = true;
        state.error = null;
        state.associate = null;
        state.isAuthenticated = false;
    },
    [AUTHENTICATING_SUCCESS](data) {
        state.isLoggedIn = true;
        state.isLoading = false;
        state.error = null;
        state.token = data.token;
        state.isAuthenticated = true;
    },
    [AUTHENTICATING_ERROR](error) {
        state.isLoading = false;
        state.error = error;
        state.isAuthenticated = false;
        state.associate = null;
    },
    [PROVIDING_DATA_ON_AUTHENTICATION](payload) {
        state.isLoggedIn = false;
        state.isLoading = false;
        state.error = null;
        state.associate = payload.associate;
    },
    logout() {
        state.isAuthenticated = false;
        state.token = null;
        state.associate = null;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
