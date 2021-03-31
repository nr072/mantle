<template>
    <div>

        <section v-if="tasks.length > 0">
            <h1>Tasks</h1>
            <ul>
                <li v-for="task in tasks" :key="task.id">
                    <task :task="task"
                        @due-time-update="updateDueTime"
                    />
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
                message: {
                    type: "",
                    content: ""
                }
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
                    .catch(error => this.handleError(error));
            },



            // Task ID and new due time are emitted from child components.
            // Afterward, all tasks are fetched again.
            updateDueTime(data) {

                const method = "put";
                const url = "/api/tasks/" + data.id;

                axios({ method, url, data })
                    .then(response => {
                        if (response.status === 200) {
                            this.message = {
                                type: "success",
                                content: "Due time updated successfully!"
                            };
                        }
                    })
                    .catch(error => this.handleError(error))
                    .then(() => this.fetchTasks());

            },



            // Error message is set.
            handleError(error) {

                this.message.type = "error";

                if (error.response !== undefined) {

                    switch (error.response.status) {
                        case 422:
                            this.message.content = "Validation error";
                            return;
                        default:
                            this.message.content = error.response.data.message;
                    }

                } else {
                    this.message.content = error.message;
                }

            },

        }

    }

</script>