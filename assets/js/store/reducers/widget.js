import { NAME_SEARCH, LEVEL_SEARCH, EMAIL_SEARCH,
     PHONE_SEARCH, DATE_SEARCH, ADD_CURRENT_PAGINATION,
     SCROLL_DOWN, ADD_MODAL, CLOSE_MODAL, LOAD_DATA, OPEN_MODAL } from '../actions/actionTypes';
import { peopleData } from './peopleData';

const initialState = {
    people: peopleData,
    associates: [],
    nameSearch: '',
    levelSearch: '',
    emailSearch: '',
    phoneSearch: '',
    dateSearch: '',
    pages: 1,
    currentPages: 1,
    scrollDown: '',
    modal: "",
    showModal: false,
    currentAssociates: [],
    firstNewAssociate: 0,
    modalOpen: false,
    modalId: ''
};

const reducer = (state = initialState, action) => {
    switch (action.type) {
        case NAME_SEARCH:
        return {
            ...state,
            nameSearch: action.name,
        };
        case LEVEL_SEARCH:
        return {
            ...state,
            levelSearch: action.level
        };
        case EMAIL_SEARCH:
        return {
            ...state,
            emailSearch: action.email
        };
        case PHONE_SEARCH:
        return {
            ...state,
            phoneSearch: action.phone
        };
        case DATE_SEARCH:
        return {
            ...state,
            dateSearch: action.date
        };
        case ADD_MODAL:
        return {
            ...state,
            modal: action.modal,
            showModal: true,
        };
        case CLOSE_MODAL:
        return {
            ...state,
            modalOpen: false,
        };
        case OPEN_MODAL:
        return {
            ...state,
            modalOpen: true,
            modalId: action.modalId
        };
        case LOAD_DATA:
        return {
            ...state,
            associates: action.associates,
            pages: action.pages,
            currentPage: action.currentPage,
        };
        default:
            return state;
    }
};

export default reducer;