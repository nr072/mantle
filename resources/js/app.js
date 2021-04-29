require('./bootstrap');

import Vue from 'vue';
import TaskList from './components/TaskList.vue';

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'test',
    wsHost: '127.0.0.1',
    wsPort: 6001,
    forceTLS: false,
    disableStats: true
});

new Vue({
    el: "#app",
    components: { TaskList }
});