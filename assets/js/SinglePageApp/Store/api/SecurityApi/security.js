import axios from "axios";
import router from "../../../Router/index";

export default {
    login(login, password) {
        return axios.post("/api/token", {
            email: login,
                password: password
        });
    },
    unAuthenticate() {
        localStorage.associate = null;
        localStorage.isAuthenticated = null;
        localStorage.token = null;
        router.push('/login');
    },
    authenticatePostApi(url, token) {
        try {
            console.log(localStorage.token);
            return axios.post(url, {
                token: token
            }, {headers: {"Authorization" : `Bearer ${localStorage.token}`} });
        } catch (e) {
            console.log(e);
            this.unAuthenticate();
        }
    },
    authenticateGetApi(url, token) {
        try {
            console.log(localStorage.token);
            return axios.get(url, {
                token: token,
                headers: {"Authorization" : `Bearer ${localStorage.token}`}
            });
        } catch (e) {
            console.log(e);
            this.unAuthenticate();
        }
    },
    }