<template>
    <div>

        <section v-if="tasks.length > 0">
            <h1>Tasks</h1>
            <ul>
                <li v-for="task in tasks" :key="task.id">
                    <task :task="task" />
                </li>
            </ul>
        </section>

        <p v-else>No task found</p>

    </div>
</template>





<script>

    import Task from './Task.vue';

    export default {

        components: { Task },

        data() {
            return {
                tasks: [],
                error: ""
            }
        },

        mounted() {

            this.fetchTasks();

        },

        methods: {

            fetchTasks() {
                axios.get("/api/tasks")
                    .then(response => {
                        if (response.status === 200) {
                            this.tasks = response.data;
                        }
                    })
                    .catch(error => {
                        this.error = error;
                        alert(error);
                    });
            }

        }

    }

</script>