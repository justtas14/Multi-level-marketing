import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import Main from './Components/Main';
import configureStore from './store/configureStore';

const store = configureStore();

const mainAction = window.mainAction ? window.mainAction : (() => {});
const mainActionLabel = window.mainActionLabel ? window.mainActionLabel :  'unknown';

const Redux = (
    <Provider store={store}>
        <Main mainAction={mainAction} mainActionLabel={mainActionLabel}/>
    </Provider>
);

ReactDOM.render(Redux, document.getElementById('root'));