const state = {
    associates: [],
    paginationInfo: {
        numberOfPages: 1,
        currentPage: 1,
    },
    nameSearchVal: '',
    emailSearchVal: '',
    phoneSearchVal: '',
    spinner: false,
};

const getters = {

};

const actions = {

};

const mutations = {
    setSpinnerState: (flag) => {
        state.spinner = flag;
    },
    loadData: ({ ...data }) => {
        state.associates = data.associates;
        state.paginationInfo.currentPage = data.currentPage;
        state.paginationInfo.numberOfPages = data.numberOfPages;
        state.spinner = false;
    },
    updateSearchVal: ({ ...params }) => {
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
    changePage: ({ page, action }) => {
        if (action == null) {
            state.paginationInfo.currentPage = page;
        } else if (action === 'add') {
            state.paginationInfo.currentPage += 1;
        } else if (action === 'subtract') {
            state.paginationInfo.currentPage += 1;
        }
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
