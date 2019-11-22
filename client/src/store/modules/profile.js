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
        // commit('setIsLoadingForm');
        const securityApiObj = new SecurityAPI();

        const response = await securityApiObj.profileUpdate(
            '/api/associate/profile',
            rootState.Security.token,
            profileFormData,
        );

        console.log(response.data);

        if (response.data.formErrors) {
            commit('setErrors', response.data.formErrors);
        } else {
            commit('profileUpdate', response.data);
        }
    },
    async home({ commit, rootState }) {
        commit('setIsLoading');
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.profileUpdate(
            '/api/associate/profile',
            rootState.Security.token,
        );

        console.log(response.data);

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
    profileUpdate: () => {

    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
