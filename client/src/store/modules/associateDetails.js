import ApiCalls from '../api/SecurityApi/apiCalls';

const initialState = {
    associateUrlId: null,
    isLoading: false,
    formError: null,
    formSuccess: {
        message: null,
        type: null,
    },
    associate: null,
    associateParent: null,
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
        commit('setIsLoading', true);
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
        commit('loadData', res.data);

        return res.data;
    },
};

const mutations = {
    setIsLoading: (state, flag) => {
        state.isLoading = flag;
    },
    setError: (state, error) => {
        state.error = error;
    },
    setAssociateUrlId: (state, id) => {
        state.associateUrlId = id;
    },
    loadData: (state, data) => {
        state.formError = data.formError;
        state.formSuccess = data.formSuccess;
        state.associate = data.associate;
        state.associateParent = data.associateParent;
        state.invitations = data.invitations;
        state.associatesInLevels = data.associatesInLevels;
        state.pagination.currentPage = data.pagination.currentPage;
        state.maxLevel = data.maxLevel;
        state.levels = data.levels;
        state.pagination.numberOfPages = data.pagination.numberOfPages;
        state.isLoading = false;
    },
    changePage: (state, { page, action }) => {
        if (action == null) {
            state.paginationInfo.currentPage = page;
        } else if (action === 'add') {
            state.paginationInfo.currentPage = Number(state.paginationInfo.currentPage) + 1;
        } else if (action === 'subtract') {
            state.paginationInfo.currentPage = Number(state.paginationInfo.currentPage) - 1;
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
