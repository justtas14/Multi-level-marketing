import {
    NAME_SEARCH, LEVEL_SEARCH, EMAIL_SEARCH,
    PHONE_SEARCH, DATE_SEARCH, ADD_CURRENT_PAGINATION,
    SCROLL_DOWN,  LOAD_DATA, SHOW_SPINNER, HIDE_SPINNER
} from '../actions/actionTypes';
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
    currentAssociates: [],
    firstNewAssociate: 0,
    spinner: false,
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
        case LOAD_DATA:
        return {
            ...state,
            associates: action.associates,
            pages: action.pages,
            currentPage: action.currentPage,
        };
        case SHOW_SPINNER:
        return {
            ...state,
            spinner: true
        };
        case HIDE_SPINNER:
        return {
            ...state,
            spinner: false
        };
        default:
            return state;
    }
};

export default reducer;