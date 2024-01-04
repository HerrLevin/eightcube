import Axios from 'axios'

const csrf = async () => {
    // check cookies for XSRF-TOKEN
    // if not found, get a new one
    if (document.cookie.split(';').some((item) => item.trim().startsWith('XSRF-TOKEN='))) {
        return
    }

    return await Axios.get(import.meta.env.VITE_PUBLIC_BACKEND_URL + '/sanctum/csrf-cookie')
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

axios.interceptors.request.use(
    async (config) => {
        await csrf()

        return config
    },
    (error) => {
        return Promise.reject(error)
    },
)

export default axios
