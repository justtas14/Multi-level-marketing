import { NAME_SEARCH, LEVEL_SEARCH, EMAIL_SEARCH, 
    PHONE_SEARCH, DATE_SEARCH, ADD_CURRENT_PAGINATION, 
    SCROLL_DOWN, ADD_MODAL, CLOSE_MODAL, LOAD_DATA } from './actionTypes';

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

export const addModal = (modal) => {
    return {
        type:ADD_MODAL,
        modal,
    };
};

export const closeModal = () => {
    return {
        type:CLOSE_MODAL,
    };
};

export const loadData = (data) => {
    return {
        type: LOAD_DATA,
        associates: data.associates,
        pages: data.pages,
        currentPage: data.currentPage,
    }
}




