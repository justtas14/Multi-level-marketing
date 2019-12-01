const initialState = {
    associates: [],
    paginationInfo: {
        numberOfPages: 1,
        currentPage: 1,
    },
    nameSearchVal: '',
    emailSearchVal: '',
    phoneSearchVal: '',
};

const getters = {

};

const actions = {
};

const mutations = {
    loadData: (state, { ...data }) => {
        state.associates = data.associates;
        state.paginationInfo.currentPage = data.currentPage;
        state.paginationInfo.numberOfPages = data.numberOfPages;
    },
    updateSearchVal: (state, { ...params }) => {
        switch (params.name) {
        case 'name':
            state.nameSearchVal = params.input;
            break;
        case 'email':
            state.emailSearchVal = params.input;
            break;
        case 'phone':
            state.phoneSearchVal = params.input;
            break;
        default:
            break;
        }
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
