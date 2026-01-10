import axios from 'axios';

/**
 * Criar uma instância do Axios com a URL base da API
 */
const http = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

/**
 * Adicionar um interceptor de requisição para incluir o cabeçalho Authorization
 */
http.interceptors.request.use(function (config) {
    const token = localStorage.getItem('user_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
}, function (error) {
    return Promise.reject(error);
});

export default http;