import VuexPersist from 'vuex-persist';

const vuexPersist = new VuexPersist({
    key: 'my-app',
    storage: window.localStorage,
});

export default vuexPersist.plugin;
