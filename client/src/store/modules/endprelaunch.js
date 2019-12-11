import ApiCalls from '../api/SecurityApi/apiCalls';

const initialState = {
    formSuccess: false,
    errorMessage: '',
    hasPrelaunchEnded: false,
    configurationContent: '',
};

const getters = {

};

const actions = {
    async home({ commit, rootState }, formData = null) {
        const apiCallObj = new ApiCalls();
        const response = await apiCallObj.endPrelaunch(
            '/api/admin/endprelaunch',
            formData,
            rootState.Security.token,
        );
        commit('setGeneralInfo', response.data);
        return response.data;
    },
};

const mutations = {
    setGeneralInfo(state, data) {
        state.formSuccess = data.formSuccess;
        state.hasPrelaunchEnded = data.hasPrelaunchEnded;
        state.configurationContent = data.configurationContent;
        state.errorMessage = data.errorMessage;
    },

    setFormSuccess(state, flag) {
        state.formSuccess = flag;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
