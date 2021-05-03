require('./bootstrap');

import Vue from 'vue';
import App from './components/App.vue';

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'test',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true
});

new Vue({
    el: "#app",
    components: { App }
});