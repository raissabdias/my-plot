import http from './http';

export default {
    
    /**
     * Buscar livros na API do Google Books
     * @param {string} query
     */
    async searchGoogle(query) {
        return await http.get(`/books/search?query=${query}`);
    },

    /**
     * Adicionar livro à estante do usuário
     * @param {object} payload 
     * @returns 
     */
    addToShelf(payload) {
        return http.post('/shelf', payload);
    },

    /**
     * Listar livros da estante do usuário
     * @returns 
     */
    getMyShelf(params = {}) {
        return http.get('/shelf', { params });
    },

    /**
     * Atualizar um livro na estante do usuário
     * @param {number} id 
     * @param {object} payload 
     * @returns 
     */
    updateShelf(id, payload) {
        return http.put(`/shelf/${id}`, payload);
    },

    /**
     * Remover um livro da estante do usuário
     * @param {number} id 
     * @returns 
     */
    removeFromShelf(id) {
        return http.delete(`/shelf/${id}`);
    },

    /**
     * Obter detalhes de um livro
     * @param {number} id 
     * @returns 
     */
    getBookDetails(id) {
        return http.get(`/book/${id}`);
    },
};