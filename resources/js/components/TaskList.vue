<template>
    <div>
        
        <ul>
            <task v-for="task of tasks" :key="task.id"
                :task="task"
                @task-update="updateTask"
                @task-removal="removeTask"
            ></task>
        </ul>

    </div>
</template>





<script>

    import Task from './Task.vue';

    export default {

        components: { Task },

        data() {
            return {
    
                tasks: []

            }
        },

        mounted() {
            this.fetchTasks();
        },

        methods: {

            fetchTasks() {
                axios.get('api/tasks')
                    .then(response => {
                        if (response.status === 200) {
                            this.tasks = response.data;
                        }
                    })
                    .catch(error => alert(error));
            },



            updateTask(data) {
                const taskId = data.id;
                const url = 'api/tasks/' + taskId;
                axios.put(url, data)
                    .catch(error => alert(error))
                    .then(() => this.fetchTasks());
            },



            removeTask(taskId) {
                const url = 'api/tasks/' + taskId;
                axios.delete(url)
                    .catch(error => alert(error))
                    .then(() => this.fetchTasks());
            }

        }

    }

</script>