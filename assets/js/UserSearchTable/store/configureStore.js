import { createStore, combineReducers } from 'redux';

import widgetReducer from './reducers/widget';

const rootReducer = combineReducers({
    widget: widgetReducer
});

const configureStore = () => {
    return createStore(rootReducer);
};

export default configureStore;
