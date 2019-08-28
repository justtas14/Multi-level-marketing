import {
    NAME_SEARCH, LEVEL_SEARCH, EMAIL_SEARCH,
    PHONE_SEARCH, DATE_SEARCH, ADD_CURRENT_PAGINATION,
    SCROLL_DOWN, LOAD_DATA, SHOW_SPINNER, HIDE_SPINNER
} from './actionTypes';

export const nameSearch = (name) => {
    return {
        type:NAME_SEARCH,
        name
    };
};
export const levelSearch = (level) => {
    return {
        type:LEVEL_SEARCH,
        level
    };
};
export const emailSearch = (email) => {
    return {
        type:EMAIL_SEARCH,
        email
    };
};
export const phoneSearch = (phone) => {
    return {
        type:PHONE_SEARCH,
        phone
    };
};
export const dateSearch = (date) => {
    return {
        type:DATE_SEARCH,
        date
    };
};

export const addCurrentPagination = (count, currentAssociates) => {
    return {
        type:ADD_CURRENT_PAGINATION,
        count,
        currentAssociates
    };
};

export const scrollDown = (position) => {
    return {
        type:SCROLL_DOWN,
        position,
    };
};

export const loadData = (data) => {
    return {
        type: LOAD_DATA,
        associates: data.associates,
        pages: data.pages,
        currentPage: data.currentPage,
    }
};

export const showSpinner = () => {
    return {
        type: SHOW_SPINNER
    }
};

export const hideSpinner = () => {
    return {
        type: HIDE_SPINNER
    }
};


