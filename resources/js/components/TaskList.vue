<template>
    <div>
        
        <ul>
            <task v-for="task of tasks" :key="task.id"
                :task="task"
                :newDateMinValue="newDateMinValue"
                :newDateMaxValue="newDateMaxValue"
                @task-update="updateTask"
                @task-removal="removeTask"
            ></task>
        </ul>

        <button v-show="!isAddingTask"
            @click="showAdderSection"
        >Add task</button>

        <task-adder v-show="isAddingTask"
            ref="taskAdderComp"
            :newDateMinValue="newDateMinValue"
            :newDateMaxValue="newDateMaxValue"
            @task-update="addTask"
            @edit-cancellation="isAddingTask = false"
        ></task-adder>

    </div>
</template>





<script>

    import Task from './Task.vue';
    import TaskAdder from './TaskEditor.vue';

    export default {

        components: {

            Task,
            TaskAdder // Used as both an editor and an adder

        },

        data() {
            return {
    
                tasks: [],

                // Used to show/hide the section for adding a new task.
                isAddingTask: false,

                // Today's date is used to set both a minimum value and
                // a maximum value for setting a due time.
                now: new Date()

            }
        },

        computed: {

            // Today's date and a date from 1 year ahead are used to set
            // the minimum and the maximum values for setting due time.
            // Since these values are unlikely to change much in a single
            // session (before the user closes the browser tab), these 2
            // are passed to all task editor components.
            newDateMinValue() {
                return this.now.toISOString().split('T')[0];
            },
            newDateMaxValue() {
                const nowPlus1Year = new Date(this.now.getTime() + 31536000000);
                return nowPlus1Year.toISOString().split('T')[0];
            }

        },

        mounted() {

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

            // The task adder section is shown and the task name field
            // is focused.
            showAdderSection() {
                this.isAddingTask = true;
                this.$nextTick(() => {
                    this.$refs.taskAdderComp.$refs.newNameInput.focus();
                });
            },

            addTask(data) {
                const url = 'api/tasks';
                axios.post(url, data)
                    .catch(error => this.showNotification('error', error.message));

                this.isAddingTask = false;
            },

            showNotification(type, content) {
                this.$emit('notification', { type, content });
            }

        }

    }

</script>