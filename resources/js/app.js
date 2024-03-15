import './bootstrap';
import { createApp } from 'vue';
import {createRouter, createWebHistory} from 'vue-router';

import Index from "./components/Index.vue";
import Login from "./components/Login.vue";
import Registration from "./components/Registration.vue";
import Home from "./components/Home.vue";

const app = createApp({});

app.component('Index', Index)


const routes = [
    { path: '/', redirect: '/login' },
    { path: '/home', name: 'home.index', component: Home },
    { path: '/login', name: 'user.login', component: Login },
    { path: '/registration', name: 'user.registration', component: Registration },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('x-xsrf-token')

    if(!token) {
        if(to.name === 'user.login' || to.name === 'user.registration') {
            return next()
        } else {
            next({ name: 'user.login' })
        }
    }

    if(to.name === 'user.login' || to.name === 'user.registration' && token) {
        return next({
            name: 'home'
        })
    }

    next()
})

app.use(router);
app.mount('#app');
