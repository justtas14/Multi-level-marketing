import ApiCalls from '../api/SecurityApi/apiCalls';

const initialState = {
    formError: null,
    formSuccess: {
        message: null,
        type: null,
    },
    stateAssociate: null,
    associateParent: null,
    associateUrlId: null,
    invitations: null,
    associatesInLevels: null,
    maxLevel: null,
    levels: null,
    pagination: {
        currentPage: 1,
    },
};

const getters = {

};

const actions = {
    async associateInfo({ commit, rootState }, formData) {
        const apiCallsObj = new ApiCalls();
        const res = await apiCallsObj.associateInfo(
            '/api/admin/users',
            formData,
            rootState.Security.token,
        );
        commit('loadData', res.data);
    },
    async buttonAction({ commit, rootState }, formData) {
        const apiCallsObj = new ApiCalls();
        const res = await apiCallsObj.associateInfo(
            '/api/admin/users',
            formData,
            rootState.Security.token,
        );
        if (res.data.formSuccess.type === 'delete') {
            commit('loadDeleteData', res.data);
        } else {
            commit('loadData', res.data);
        }
        return res.data;
    },
};

const mutations = {
    setError: (state, error) => {
        state.error = error;
    },
    setAssociateUrlId: (state, id) => {
        state.associateUrlId = id;
    },
    loadDeleteData: (state, data) => {
        state.formError = data.formError;
        state.formSuccess = data.formSuccess;
    },
    loadData: (state, data) => {
        state.formError = data.formError;
        state.formSuccess = data.formSuccess;
        state.stateAssociate = data.associate;
        state.associateParent = data.associateParent;
        state.invitations = data.invitations;
        state.associatesInLevels = data.associatesInLevels;
        state.pagination = data.pagination;
        state.maxLevel = data.maxLevel;
        state.levels = data.levels;
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
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
