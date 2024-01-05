import Axios from 'axios'
import router from "@/router";

const csrf = async () => {
    // check cookies for XSRF-TOKEN
    // if not found, get a new one
    if (document.cookie.split(';').some((item) => item.trim().startsWith('XSRF-TOKEN='))) {
        return
    }

    return await Axios.get(import.meta.env.VITE_PUBLIC_BACKEND_URL + '/sanctum/csrf-cookie', {withCredentials: true});
}

// create axios instance with interceptors
const axios = Axios.create({
    baseURL: import.meta.env.VITE_PUBLIC_BACKEND_URL,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    withXSRFToken: true,
})

async function onFulfilled(config) {
    await csrf();

    return config;
}

function onRejected(error) {
    if (error.response.status === 401) {
        router.push({name: 'logout'});
    }
    return Promise.reject(error);
}

axios.interceptors.request.use(onFulfilled, onRejected);
axios.interceptors.response.use((response) => {return response}, onRejected);

export default axios
