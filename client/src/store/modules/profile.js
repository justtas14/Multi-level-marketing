import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    formErrors: null,
    formUpdated: false,
};
const getters = {

};

const actions = {
    async submitForm({ commit, rootState }, profileFormData) {
        commit('profileUpdate', false);
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.profileUpdate(
            '/api/associate/profile',
            rootState.Security.token,
            profileFormData,
        );

        if (!response.data.updated) {
            commit('setErrors', response.data.formErrors);
        } else {
            commit('setErrors', response.data.formErrors);
            commit('profileUpdate', response.data.updated);
            return response.data.associate;
        }
        return false;
    },
    async home({ commit, rootState }) {
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.profileUpdate(
            '/api/associate/profile',
            rootState.Security.token,
        );

        commit('setHomeInfo', response.data);
        return response.data;
    },
};

const mutations = {
    setErrors: (state, errors) => {
        state.formErrors = errors;
    },
    setHomeInfo: (state, data) => {
        state.formErrors = data.formErrors;
    },
    profileUpdate: (state, flag) => {
        state.formUpdated = flag;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
