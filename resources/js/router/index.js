import { createRouter, createWebHistory } from 'vue-router';

import AuthService from '../services/AuthService';

import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import MyLibrary from '../views/MyLibrary.vue';
import Dashboard from '../views/Dashboard.vue';
import Profile from '../views/Profile.vue';
import BookDetails from '../views/BookDetails.vue';
import LandingPage from '../views/LandingPage.vue';

const routes = [
    { path: '/login', name: 'Login', component: Login, meta: { guest: true, title: 'Entrar' } },
    { path: '/register', name: 'Register', component: Register, meta: { guest: true, title: 'Cadastrar' } },
    { path: '/library', name: 'MyLibrary', component: MyLibrary, meta: { requiresAuth: true, title: 'Minha Estante' } },
    { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true, title: 'Painel' } },
    { path: '/profile', name: 'Profile', component: Profile, meta: { requiresAuth: true, title: 'Perfil' } },
    { path: '/book/:id', name: 'Book', component: BookDetails, meta: { requiresAuth: false, title: 'Livro' } },
    { path: '/', name: 'LandingPage', component: LandingPage, meta: { guest: true, title: 'Início' } },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

const DEFAULT_TITLE = 'MyPlot';

router.beforeEach((to, from, next) => {
    document.title = to.meta.title ? `${to.meta.title} | ${DEFAULT_TITLE}` : DEFAULT_TITLE;

    const isAuthenticated = AuthService.isAuthenticated();
    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'LandingPage' });
    } 
    else if (to.meta.guest && isAuthenticated) {
        next({ name: 'Dashboard' });
    } 
    else {
        next();
    }
});

export default router;