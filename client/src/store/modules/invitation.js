import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    isLoading: false,
    isLoadingForm: false,
    isLoadingSentInvitations: false,
    isResendBtnLoading: true,
    formErrors: null,
    sent: null,
    invitations: null,
    pagination: null,
    uniqueAssociateInvitationLink: null,
    siteKey: null,
    submitLabel: 'send',
};
const getters = {

};

const actions = {
    async submitInvitationForm({ commit, rootState }, payload) {
        commit('setIsLoadingForm');
        const SecurityApiObj = new SecurityAPI(
            payload.dependencies.logout,
            payload.dependencies.router,
        );
        const response = await SecurityApiObj.authenticateInvitationPostApi(
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
        const SecurityApiObj = new SecurityAPI(
            payload.dependencies.logout,
            payload.dependencies.router,
        );
        const response = await SecurityApiObj.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload.params,
        );
        commit('setSent', response.data.sent);
    },
    async changePage({ state, commit, rootState }, dependencies) {
        commit('setIsLoadingSentInvitations');
        const payload = {
            page: state.pagination.currentPage,
        };
        const SecurityApiObj = new SecurityAPI(dependencies.logout, dependencies.router);
        const response = await SecurityApiObj.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload,
        );
        commit('setGeneralInvitationInfo', response.data);
    },
    async invitationHome({ commit, rootState }, dependencies) {
        commit('setIsLoading');
        const payload = {};
        const SecurityApiObj = new SecurityAPI(dependencies.logout, dependencies.router);
        const response = await SecurityApiObj.authenticateInvitationPostApi(
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
            state.pagination.currentPage += 1;
        } else if (action === 'subtract') {
            state.pagination.currentPage -= 1;
        }
    },
};

export default {
    initialState,
    getters,
    actions,
    mutations,
};
