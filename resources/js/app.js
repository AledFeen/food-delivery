import './bootstrap';
import { createApp } from 'vue';

import {createRouter, createWebHistory} from 'vue-router';

import Index from "./components/Index.vue";
import Login from "./components/Login.vue";
import Registration from "./components/Registration.vue";
import Home from "./components/Home.vue";
import MyStore from "./components/MyStore.vue";
import Admin from "./components/Admin.vue";
import Courier from "./components/Courier.vue";
import Users from "./components/Users.vue";
import TestComp from "./components/testComp.vue";
import AddStore from "./components/AddStore.vue";

const app = createApp({});

app.component('Index', Index)


const routes = [
    { path: '/', redirect: '/login' },
    { path: '/home', name: 'home.index', component: Home },

    { path: '/login', name: 'user.login', component: Login },
    { path: '/registration', name: 'user.registration', component: Registration },
    { path: '/myStore', name: 'user.store', component: MyStore },
    { path: '/adminPanel', name: 'user.admin', component: Admin },
    { path: '/courierPanel', name: 'user.courier', component: Courier },

    { path: '/users', name: 'admin.users', component: Users },
    { path: '/addStore/:userId', name: 'admin.addStore', props: true, component: AddStore },

    { path: '/test', name: 'test', component: TestComp},

]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('x-xsrf-token')
    const isStore = localStorage.getItem('isStore')
    const isAdmin = localStorage.getItem('isAdmin')
    const isCourier = localStorage.getItem('isCourier')

    ////защитить /myStore от входа по урл
    if (!token) {
        if (to.name === 'user.login' || to.name === 'user.registration' || to.name === 'home.index') {
            return next();
        } else {
            return next({ name: 'user.login' });
        }
    } else {
        switch (to.name) {
            case 'user.login':
            case 'user.registration':
                next({ name: 'home.index' });
                break;
            case 'user.store':
                if (isStore === 'false') {
                    next({ name: 'home.index' });
                } else {
                    next();
                }
                break;
            case 'user.admin':
                if (isAdmin === 'false') {
                    next({ name: 'home.index' });
                } else {
                    next();
                }
                break;
            case 'user.courier':
                if (isCourier === 'false') {
                    next({ name: 'home.index' });
                } else {
                    next();
                }
                break;
            case 'admin.users':
                if (isAdmin === 'false') {
                    next({ name: 'home.index' });
                } else {
                    next();
                }
                break;
            case 'admin.addStore':
                if (isAdmin === 'false') {
                    next({ name: 'home.index' });
                } else {
                    next();
                }
                break;
            default:
                next();
        }
    }
})

app.use(router);
app.mount('#app');
