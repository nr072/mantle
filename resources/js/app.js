require('./bootstrap');

import Vue from 'vue';
import TaskList from './components/TaskList.vue';

new Vue({
    el: "#app",
    components: { TaskList }
});