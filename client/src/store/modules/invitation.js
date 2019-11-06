import SecurityAPI from "../api/SecurityApi/security";

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
    submitLabel: 'send'
};
const getters = {

};

const actions = {
    async submitInvitationForm({commit, state, rootState}, payload) {
        commit('setIsLoadingForm');
        let response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload
        );
        if (response.data.formErrors) {
            commit('setErrors', response.data.formErrors);
        } else {
            commit('setSent', response.data.sent);
        }
    },
    async resendInvitation({commit, state, rootState}, payload) {
        let response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload
        );
        commit('setSent', response.data.sent);
    },
    async changePage({commit, state, rootState}) {
        commit('setIsLoadingSentInvitations');
        let payload = {
            page: state.pagination.currentPage
        };
        console.log(payload);
        let response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload
        );
        commit('setGeneralInvitationInfo', response.data);
    },
    async invitationHome({commit, state, rootState}, payload = {}) {
        commit('setIsLoading');
        let response = await SecurityAPI.authenticateInvitationPostApi(
            '/api/associate/invite',
            rootState.Security.token,
            payload
        );
        commit('setGeneralInvitationInfo', response.data);
    }
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
    setErrors: (state, errors) => {
        state.isLoadingForm = false;
        state.formErrors = errors;
    },
    setSent: (state, sent) => {
        state.isLoadingForm = false;
        state.formErrors = null;
        state.sent = sent;
    },
    setNotSent: (state) => {
        state.isLoadingForm = false;
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
    changePagination: (state, {page, action}) => {
        if (action == null) {
            state.pagination.currentPage = page;
        } else if (action == 'add') {
            state.pagination.currentPage++;
        } else if (action == 'subtract') {
            state.pagination.currentPage--;
        }
    },
};

export default {
    state,
    getters,
    actions,
    mutations
};
