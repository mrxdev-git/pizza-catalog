import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

import axios from 'axios';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.min.js'

import App from './components/App.vue';

import Ingredients from './components/Ingredients.vue';
import Pizzas from './components/Pizzas.vue';

const routes = [
    { path: '/', redirect: '/ingredients' },
    { path: '/ingredients', component: Ingredients },
    { path: '/pizzas', component: Pizzas }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

const app = createApp(App);

app.use(router);

app.config.globalProperties.$http = axios;

app.mount('#app');
