<template>
    <div>

        <notification-area :message="message"></notification-area>

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
    import NotificationArea from './NotificationArea.vue';

    export default {

        components: { Task, NotificationArea },

        data() {
            return {

                tasks: [],

                // The 'canShow' variable determines whether the notification
                // will be shown or not.
                message: {
                    canShow: false,
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
                                canShow: true,
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

                // The error message text is set based on the nature of
                // the error. For example, if there is a network error,
                // the 'response' property is not present; in that case,
                // the 'message' value is used.
                let text = "";

                if (error.response === undefined) {

                    text = error.message;

                } else {

                    const status = error.response.status;
                    text = (
                        status === 422 ? "Validation error"
                            : error.response.data.message
                    );

                }

                this.message = {
                    canShow: true,
                    type: "error",
                    content: text
                };

            },

        }

    }

</script>