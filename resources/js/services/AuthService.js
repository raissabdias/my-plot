import http from './http';

export default {
    /**
     * Enviar dados para autenticação (Login)
     * @param {String} email
     * @param {String} password
     */
    async login(email, password) {
        const response = await http.post(`/login`, {
            email: email,
            password: password
        });

        if (response.data.access_token) {
            localStorage.setItem('user_token', response.data.access_token);
            localStorage.setItem('user_data', JSON.stringify(response.data.user));
        }

        return response.data;
    },

    /**
     * Apaga o token do navegador (Logout)
     */
    logout() {
        http.post('/logout').catch(() => {});

        localStorage.removeItem('user_token');
        localStorage.removeItem('user_data');
    },

    /**
     * Verifica se o usuário está autenticado
     */
    isAuthenticated() {
        const token = localStorage.getItem('user_token');
        return !!token; 
    }
};