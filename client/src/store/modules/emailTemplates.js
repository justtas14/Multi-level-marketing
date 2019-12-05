import ApiCalls from '../api/SecurityApi/apiCalls';

const initialState = {
    urlType: null,
    availableParameters: [],
    emailTemplate: {
        emailBody: '',
        emailSubject: '',
        emailType: '',

    },
    title: '',
    formError: '',
    formSuccess: false,
};

const getters = {

};

const actions = {
    async home({ state, commit, rootState }, formData = null) {
        const apiCallObj = new ApiCalls();
        const response = await apiCallObj.endPrelaunch(
            `/api/admin/emailtemplate/${state.urlType}`,
            formData,
            rootState.Security.token,
        );
        console.log(response.data);
        commit('setGeneralInfo', response.data);
    },
};

const mutations = {
    setGeneralInfo(state, data) {
        state.title = data.title;
        state.availableParameters = data.availableParameters;
        state.emailTemplate = data.emailTemplate;
        state.formSuccess = data.formSuccess;
        state.formError = data.formErrors;
    },

    setUrlType(state, type) {
        state.urlType = type;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
