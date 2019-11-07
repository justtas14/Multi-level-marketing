import SecurityAPI from '../api/SecurityApi/apiCalls';

const state = {
    isLoading: false,
    isLoadingForm: false,
    isLoadingSentInvitations: false,
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
        const response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload,
        );
        if (response.data.formErrors) {
            commit('setErrors', response.data.formErrors);
        } else {
            commit('setSent', response.data.sent);
        }
    },
    async resendInvitation({ commit, rootState }, payload) {
        const response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload,
        );
        commit('setSent', response.data.sent);
    },
    async changePage({ commit, rootState }) {
        commit('setIsLoadingSentInvitations');
        const payload = {
            page: state.pagination.currentPage,
        };
        console.log(payload);
        const response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload,
        );
        commit('setGeneralInvitationInfo', response.data);
    },
    async invitationHome({ commit, rootState }, payload = {}) {
        commit('setIsLoading');
        const response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload,
        );
        commit('setGeneralInvitationInfo', response.data);
    },
};

const mutations = {
    setIsLoading: () => {
        state.isLoading = true;
    },
    setIsLoadingForm: () => {
        state.isLoadingForm = true;
    },
    setIsLoadingSentInvitations: () => {
        state.isLoadingSentInvitations = true;
    },
    setErrors: (errors) => {
        state.isLoadingForm = false;
        state.formErrors = errors;
    },
    setSent: (sent) => {
        state.isLoadingForm = false;
        state.formErrors = null;
        state.sent = sent;
    },
    setNotSent: () => {
        state.isLoadingForm = false;
        state.formErrors = null;
        state.sent = null;
    },
    setGeneralInvitationInfo: (data) => {
        state.isLoading = false;
        state.isLoadingSentInvitations = false;
        state.invitations = data.invitations;
        state.pagination = data.pagination;
        state.uniqueAssociateInvitationLink = data.uniqueAssociateInvitationLink;
        state.siteKey = data.siteKey;
        state.submitLabel = data.submitLabel;
    },
    changePagination: ({ page, action }) => {
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
    state,
    getters,
    actions,
    mutations,
};
