<template>
    <div>

        <notification-area :message="message"
            @notification-clear="clearNotification"
        ></notification-area>

        <section v-if="tasks.length > 0">
            <h1>Tasks</h1>
            <ul>
                <li v-for="task in tasks" :key="task.id">
                    <task :task="task"
                        @due-time-update="updateDueTime"
                        @task-removal="removeTask"
                    />
                </li>
            </ul>
        </section>

        <p v-else>No task found</p>

        <button :class="{ hidden: isAddingNewTask }"
            @click="isAddingNewTask = true"
        >Add task</button>

        <section :class="{ hidden: !isAddingNewTask }">

            <input type="text" v-model="newTaskName">
            <input type="date"
                :min="now.toISOString().split('T')[0]"
                :max="nowPlus1Year.toISOString().split('T')[0]"
                v-model="newTaskDateInput"
            >
            <input type="text" class="time-input"
                placeholder="23:01"
                v-model="newTaskTimeInput"
                @keyup.enter="addNewTask"
            >

            <button
                @click="addNewTask"
                :disabled="!canShowAdderConfirmBtn"
            >Confirm</button>
            <button class="btn"
                @click="isAddingNewTask = false; clearNewTaskInputs();"
            >Cancel</button>

        </section>

    </div>
</template>





<style scoped>

    .time-input {
        max-width: 50px;
    }

</style>





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
                },

                // Inputs for adding a new task are usually hidden.
                isAddingNewTask: false,

                // Current time is used to set the minimum value for
                // setting due times for tasks.
                now: new Date(),

                newTaskName: "",

                // Date and time inputs come from two different input
                // fields and are merged together before sending to the
                // back-end.
                newTaskDateInput: "",
                newTaskTimeInput: ""

            }
        },

        mounted() {

            this.fetchTasks();

        },

        computed: {

            // A date 365 days later is used to set the maximum limit
            // for the due time's date input.
            nowPlus1Year() {
                return new Date( this.now.getTime() + (3600000 * 24 * 365) );
            },

            // The confirm button for the new task adder can be shown
            // only when the inputs are revealed and the task name input
            // has a value.
            canShowAdderConfirmBtn() {
                return (
                    this.isAddingNewTask
                    && this.newTaskName.trim() !== ""
                );
            }

        },

        methods: {

            // Tasks are fetched.
            fetchTasks() {
                axios.get("api/tasks")
                    .then(response => {
                        if (response.status === 200) {
                            this.tasks = response.data;
                        }
                    })
                    .catch(error => this.handleError(error));
            },



            // Data is sent using 'POST' or 'PUT'. Used for updating or
            // removing due time.
            postOrPutData(method, url, data, successMsg, callback) {

                if (method === undefined || method.trim() === "") {
                    alert("Method not specified");
                    return;
                }

                if (method !== "post" && method !== "put") {
                    alert("Incorrect method");
                    return;
                }

                if (url === undefined || url.trim() === "") {
                    alert("URL not specified");
                    return;
                }

                if (data === undefined && data.trim() === "") {
                    alert("Data not provided");
                    return;
                }

                if (successMsg === undefined || successMsg.trim() === "") {
                    successMsg = "Action performed successfully";
                }

                // Tasks are fetched again afterward.
                axios({ method, url, data })
                    .then(response => {
                        if (response.status === 200) {
                            this.setNotification(true, "success", successMsg);
                        }
                    })
                    .catch(error => this.handleError(error))
                    .then(() => callback && callback());

            },



            // Task ID and new due time are emitted from child components.
            // Afterward, all tasks are fetched again.
            updateDueTime(data) {

                const method = "put";
                const url = "api/tasks/" + data.id;

                // Updating and removing due time works similarly, except
                // no due time is provided for removal.
                const successMsg = (
                    data.dueTime === undefined
                        ? "Due time removed successfully!"
                        : "Due time updated successfully!"
                );

                const callback = this.fetchTasks;
                this.postOrPutData(method, url, data, successMsg, callback);

            },



            // Error message is set based on error type.
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
                        status === 422 ? "Validation error" : (
                            status === 500 ? "Server error"
                                : error.response.data.message
                        )
                    );

                }

                this.setNotification(true, "error", text);

            },



            // A new task is created using the task name input, and the
            // date and time inputs (optional). Then tasks are fetched
            // again to update the task list.
            addNewTask() {

                if (this.newTaskName.trim() === "") {
                    alert("Task name not provided");
                    return;
                }

                const method = "post";
                const url = "api/tasks/";
                const successMsg = "Task created successfully";

                const data = {
                    name: this.newTaskName
                };

                // Due time may or may not be provided for a new task.
                // If both the date and the time inputs are provided,
                // due time is made from them and inserted into the
                // data that will be sent to the back-end.
                if (
                    this.newTaskDateInput !== ""
                    && this.newTaskTimeInput.trim() !== ""
                ) {

                    const newDueTimeString = (
                        this.newTaskDateInput
                        + " "
                        + this.newTaskTimeInput.trim()
                    );
                    const newDueTime = new Date(newDueTimeString).getTime();

                    // If valid, the due time is inserted into the data.
                    if (!isNaN(newDueTime)) {
                        data.dueTime = newDueTimeString;
                    }

                }

                // A new task is created and then tasks are fetched again.
                const callback = this.fetchTasks;
                this.postOrPutData(method, url, data, successMsg, callback);

                // Input fields are cleared and hidden.
                this.clearNewTaskInputs();
                this.isAddingNewTask = false;

            },



            // New task adder input fields are cleared.
            clearNewTaskInputs() {
                this.newTaskName = "";
                this.newTaskDateInput = "";
                this.newTaskTimeInput = "";
            },



            clearNotification() {
                this.message = {};
            },



            // A new notification can be set by passing values and setting
            // 'true' as the value of the 'canShow' boolean property.
            setNotification(canShow, type, content) {
                this.message = { canShow, type, content };
            },



            // An existing task is removed and tasks are fetched again.
            // The task ID is emitted from the relevant 'Task' component.
            removeTask(data) {

                if (data === undefined || data.id === undefined) {
                    alert("Task id not provided");
                    return;
                }

                const url = "api/tasks/" + data.id;
                const successMsg = "Task removed successfully";

                axios.delete(url)
                    .then(response => {
                        if (response.status === 200) {
                            this.setNotification(true, "success", successMsg);
                        }
                    })
                    .catch(error => this.handleError(error))
                    .then(() => this.fetchTasks());

            }

        }

    }

</script>