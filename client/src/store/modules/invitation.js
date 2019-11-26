import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    isLoading: false,
    isLoadingForm: false,
    isLoadingSentInvitations: false,
    isResendBtnLoading: false,
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
        commit('setIsLoadingForm');
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
        commit('setIsLoadingResendBtn');
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
        commit('setIsLoadingSentInvitations');
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
        commit('setIsLoading');
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
    setIsLoading: (state) => {
        state.isLoading = true;
    },
    setIsLoadingForm: (state) => {
        state.isLoadingForm = true;
    },
    setIsLoadingSentInvitations: (state) => {
        state.isLoadingSentInvitations = true;
    },
    setIsLoadingResendBtn: (state) => {
        state.isResendBtnLoading = true;
    },
    setErrors: (state, errors) => {
        state.isLoadingForm = false;
        state.isResendBtnLoading = false;
        state.formErrors = errors;
    },
    setSent: (state, sent) => {
        state.isLoadingForm = false;
        state.isResendBtnLoading = false;
        state.formErrors = null;
        state.sent = sent;
    },
    setNotSent: (state) => {
        state.isLoadingForm = false;
        state.isResendBtnLoading = false;
        state.formErrors = null;
        state.sent = null;
    },
    setGeneralInvitationInfo: (state, data) => {
        state.isLoading = false;
        state.isLoadingSentInvitations = false;
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
            state.pagination.currentPage = Number(state.paginationInfo.currentPage) + 1;
        } else if (action === 'subtract') {
            state.pagination.currentPage = Number(state.paginationInfo.currentPage) - 1;
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
