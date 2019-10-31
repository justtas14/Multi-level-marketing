import axios from "axios";
import router from "../../../Router/index";
import store from '../../../Store';

export default {
    login(login, password) {
        return axios.post("/api/token", {
            email: login,
                password: password
        });
    },
    unAuthenticate() {
        store.commit('Security/logout');
        router.push('/login');
    },
    authenticatePostApi(url, token) {
        try {
            return axios.post(url, {
                token: token
            }, {headers: {"Authorization" : `Bearer ${token}`} });
        } catch (e) {
            this.unAuthenticate();
        }
    },
    authenticateGetApi(url, token) {
        try {
            return axios.get(url, {
                token: token,
                headers: {"Authorization" : `Bearer ${token}`}
            });
        } catch (e) {
            this.unAuthenticate();
        }
    },
    }