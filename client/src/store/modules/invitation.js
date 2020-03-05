import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    formErrors: null,
    sent: null,
    invitations: null,
    pagination: null,
    uniqueAssociateInvitationLink: null,
    siteKey: null,
    submitLabel: 'send',
    verifyResponseKey: null,
};
const getters = {
};

const actions = {
    async submitInvitationForm({ commit, rootState }, payload) {
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload.formData,
        );
        if (response.data.formErrors) {
            commit('setErrors', response.data.formErrors);
        } else {
            commit('setSent', response.data.sent);
        }
    },
    async resendInvitation({ commit, rootState }, payload) {
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload.params,
        );
        if (response.data.formErrors) {
            commit('setErrors', response.data.formErrors);
        } else {
            commit('setSent', response.data.sent);
        }
    },
    async changePage({ state, commit, rootState }) {
        const payload = {
            page: state.pagination.currentPage,
        };
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload,
        );
        commit('setGeneralInvitationInfo', response.data);
    },
    async invitationHome({ commit, rootState }) {
        const payload = {};
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload,
        );
        commit('setGeneralInvitationInfo', response.data);
    },
};

const mutations = {
    setErrors: (state, errors) => {
        state.formErrors = errors;
    },
    setSent: (state, sent) => {
        state.formErrors = null;
        state.sent = sent;
    },
    setNotSent: (state) => {
        state.formErrors = null;
        state.sent = null;
    },
    setGeneralInvitationInfo: (state, data) => {
        state.invitations = data.invitations;
        state.pagination = data.pagination;
        state.uniqueAssociateInvitationLink = data.uniqueAssociateInvitationLink;
        state.siteKey = data.siteKey;
        state.submitLabel = data.submitLabel;
    },
    changePagination: (state, { page, action }) => {
        if (action == null) {
            state.pagination.currentPage = page;
        } else if (action === 'add') {
            state.pagination.currentPage = Number(state.pagination.currentPage) + 1;
        } else if (action === 'subtract') {
            state.pagination.currentPage = Number(state.pagination.currentPage) - 1;
        }
    },
    changeRecaptchaKey: (state, verifyResponseKey) => {
        state.verifyResponseKey = verifyResponseKey;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
