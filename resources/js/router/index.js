import { createRouter, createWebHistory } from 'vue-router';

import AuthService from '../services/AuthService';

import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import MyLibrary from '../views/MyLibrary.vue';
import Dashboard from '../views/Dashboard.vue';
import Profile from '../views/Profile.vue';
import BookDetails from '../views/BookDetails.vue';

const routes = [
    { path: '/login', name: 'Login', component: Login, meta: { guest: true } },
    { path: '/register', name: 'Register', component: Register, meta: { guest: true } },
    { path: '/library', name: 'MyLibrary', component: MyLibrary, meta: { requiresAuth: true } },
    { path: '/', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/profile', name: 'Profile', component: Profile, meta: { requiresAuth: true } },
    { path: '/book/:id', name: 'Book', component: BookDetails, meta: { requiresAuth: false } },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = AuthService.isAuthenticated();

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'Login' });
    } 
    else if (to.meta.guest && isAuthenticated) {
        next({ name: 'Dashboard' });
    } 
    else {
        next();
    }
});

export default router;