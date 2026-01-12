import http from './http';

export default {
    /**
     * Buscar todos os livros
     */
    async getAll(params = {}) {
        return await http.get('/books', { params }); 
    },

    /**
     * Adicionar um novo livro
     * @param {object} book 
     */
    async create(book) {
        return await http.post('/books', book);
    },

    /**
     * Atualizar um livro existente
     * @param {number} id 
     * @param {object} book 
     */
    async update(id, book) {
        return await http.put(`/books/${id}`, book);
    },

    /**
     * Remover um livro
     * @param {number} id 
     */
    async delete(id) {
        return await http.delete(`/books/${id}`);
    },

    /**
     * Buscar livros na API do Google Books
     * @param {string} query
     */
    async searchGoogle(query) {
        return await http.get(`/books/search?query=${query}`);
    }
};