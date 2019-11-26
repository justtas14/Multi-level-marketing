import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    isLoading: false,
    isLoadingForm: false,
    formErrors: null,
    formUpdated: false,
};
const getters = {

};

const actions = {
    async submitForm({ commit, rootState }, profileFormData) {
        commit('setIsLoadingForm');
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
        commit('setIsLoading');
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.profileUpdate(
            '/api/associate/profile',
            rootState.Security.token,
        );

        commit('setHomeInfo', response.data);
    },
};

const mutations = {
    setIsLoading: (state) => {
        state.isLoading = true;
    },
    setIsLoadingForm: (state) => {
        state.isLoadingForm = true;
    },
    setErrors: (state, errors) => {
        state.isLoadingForm = false;
        state.formErrors = errors;
    },
    setHomeInfo: (state, data) => {
        state.isLoading = false;
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
