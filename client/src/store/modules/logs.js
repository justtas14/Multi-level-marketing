import axios from 'axios';

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
    async findAll({ state, commit }) {
        const res = await axios.get('/admin/get-logs', {
            params: {
                page: state.paginationInfo.currentPage,
            },
        });
        const data = {
            logs: res.data.logs,
            pagination: res.data.pagination,
        };
        commit('setSpinnerState', false);
        commit('loadData', data);
    },
    async findBy({ commit }, params) {
        const res = await axios.get('/admin/get-logs', params);
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
            state.paginationInfo.currentPage += 1;
        } else if (action === 'subtract') {
            state.paginationInfo.currentPage -= 1;
        }
    },
};

export default {
    initialState,
    getters,
    actions,
    mutations,
};
