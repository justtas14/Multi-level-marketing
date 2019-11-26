import ApiCalls from '../api/SecurityApi/apiCalls';

const initialState = {
    logs: [],
    paginationInfo: {
        numberOfPages: 1,
        currentPage: 1,
    },
    spinner: false,
};

const getters = {

};

const actions = {
    async findLogs({ state, commit, rootState }) {
        const apiCallsObj = new ApiCalls();
        const res = await apiCallsObj.getLogs(
            '/api/admin/get-logs',
            state.paginationInfo.currentPage,
            rootState.Security.token,
        );
        const data = {
            logs: res.data.logs,
            pagination: res.data.pagination,
        };
        commit('setSpinnerState', false);
        commit('loadData', data);
    },
};

const mutations = {
    setSpinnerState: (state, flag) => {
        state.spinner = flag;
    },
    loadData: (state, { ...data }) => {
        state.logs = data.logs;
        state.paginationInfo.currentPage = data.pagination.currentPage;
        state.paginationInfo.numberOfPages = data.pagination.maxPage;
        state.spinner = false;
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
