<template>
        
    <ul>
        <task v-for="task of tasks" :key="task.id"
            :task="task"
            :newDateMinValue="newDateMinValue"
            :newDateMaxValue="newDateMaxValue"
            @task-update="updateTask"
            @task-removal="removeTask"
        ></task>
    </ul>

</template>





<script>

    import Task from './Task.vue';

    export default {

        props: {

            newDateMinValue: String,
            newDateMaxValue: String

        },

        components: {

            Task

        },

        data() {
            return {
    
                tasks: []

            }
        },

        created() {

            this.fetchTasks();

            // Echo listens for a broadcast message and fetches updated
            // tasks whenever it gets a message.
            window.Echo.channel('tasks')
                .listen('TaskUpdated', (data) => {

                    this.tasks = data.tasks;

                    // Notification is cleared (if any).
                    this.$emit('notification', {});

                });

            // TODO: Show error if offline

        },

        methods: {

            fetchTasks() {
                axios.get('api/tasks')
                    .then(response => {
                        if (response.status === 200) {
                            this.tasks = response.data;
                        }
                    })
                    .catch(error => this.showNotification('error', error.message));
            },

            // Since data is passed from task editor components, which
            // do not have task IDs, the IDs are set here before hitting
            // the API.
            updateTask(data) {
                const taskId = data.id;
                const url = 'api/tasks/' + taskId;
                axios.put(url, data)
                    .catch(error => this.showNotification('error', error.message));
            },

            removeTask(taskId) {
                const url = 'api/tasks/' + taskId;
                axios.delete(url)
                    .catch(error => this.showNotification('error', error.message));
            },

            showNotification(type, content) {
                this.$emit('notification', { type, content });
            }

        }

    }

</script>