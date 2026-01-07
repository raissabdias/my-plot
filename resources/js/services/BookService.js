import axios from 'axios';

const API_URL = '/api/books';

export default {
    /**
     * Busca todos os livros da estante
     */
    getAll() {
        return axios.get(API_URL);
    },

    /**
     * Cria um novo livro
     * @param {Object} bookData 
     */
    create(bookData) {
        return axios.post(API_URL, bookData);
    },

    /**
     * Busca no Google Books (via nosso Backend)
     * @param {String} query Termo de busca
     */
    searchGoogle(query) {
        return axios.get(`${API_URL}/search`, {
            params: { q: query }
        });
    },

    /**
     * Atualiza um livro existente
     * @param {Number} id - ID do livro
     * @param {Object} bookData - Dados atualizados
     */
    update(id, bookData) {
        return axios.put(`${API_URL}/${id}`, bookData);
    },

    /**
     * Remove um livro da estante
     * @param {Number} id - O ID do livro a ser apagado
     */
    delete(id) {
        return axios.delete(`${API_URL}/${id}`);
    },
};