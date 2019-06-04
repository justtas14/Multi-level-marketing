import { NAME_SEARCH, LEVEL_SEARCH, EMAIL_SEARCH,
     PHONE_SEARCH, DATE_SEARCH, ADD_CURRENT_PAGINATION, 
     SCROLL_DOWN, ADD_MODAL, CLOSE_MODAL, LOAD_DATA } from '../actions/actionTypes';
import { peopleData } from './peopleData';

const initialState = {
    people: peopleData,
    associates: [],
    nameSearch: '',
    levelSearch: '',
    emailSearch: '',
    phoneSearch: '',
    dateSearch: '',
    paginationIndex: 10,
    currentPagination: 1,
    scrollDown: '',
    modal: {
        name: '',
        level: '',
        email: '',
        phone: '',
        date: '',
        photo: '',
    },
    showModal: false,
    currentAssociates: [],
    firstNewAssociate: 0,
}

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
                levelSearch: action.level,    
            };
            case EMAIL_SEARCH:
            return { 
                ...state,
                emailSearch: action.email,    
            };
            case PHONE_SEARCH:
            return { 
                ...state,
                phoneSearch: action.phone,    
            };
            case DATE_SEARCH:
            return { 
                ...state,
                dateSearch: action.date,  
            };
            case ADD_CURRENT_PAGINATION:
                return { 
                    ...state,
                    currentPagination: action.count,
                    currentAssociates: action.currentAssociates,
                    firstNewAssociate: action.currentAssociates.length - state.paginationIndex 
                };
            case SCROLL_DOWN:
                return { 
                    ...state,
                    scrollDown: action.position,  
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
                    showModal: false,  
                };
            case LOAD_DATA:
                return { 
                    ...state,
                    associates: action.associates,
                };
        default:
            return state;
    }
};

export default reducer;