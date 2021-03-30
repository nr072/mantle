require('./bootstrap');

import Vue from 'vue';
import TaskList from './components/TaskList.vue';

new Vue({
    el: "#main",
    components: { TaskList }
});