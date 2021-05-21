<template>
        
    <ul v-if="openNoteId">

        <p v-if="isLoading">Loading ...</p>

        <template v-else-if="tasks.length">

            <task v-for="task of tasks" :key="task.id"
                :task="task"
                :newDateMinValue="newDateMinValue"
                :newDateMaxValue="newDateMaxValue"
                @task-update="updateTask"
                @task-removal="removeTask"
            ></task>

        </template>

        <p v-else>No task found</p>

    </ul>

    <div v-else class="content">
        <p>No note selected</p>
        <p>Select one on the note card to see tasks here</p>
    </div>

</template>





<script>

    import Task from './Task.vue';

    export default {

        props: {

            newDateMinValue: String,
            newDateMaxValue: String,

            // If the user has clicked on a note on the note card, its ID
            // is used to show its tasks on the task card.
            openNoteId: Number

        },

        components: {

            Task

        },

        data() {
            return {
    
                tasks: [],

                // A "loading" message may need to be shown in some cases
                // (e.g., fetching data).
                isLoading: false

            }
        },

        methods: {

            fetchTasks(noteId) {
                const url = '/api/notes/' + noteId;
                axios.get(url)
                    .then(response => {
                        if (response.status === 200) {
                            this.tasks = response.data.tasks;
                        }
                    })
                    .catch(error => this.$emit('notification', {
                        type: 'error',
                        content: error.message
                    }))
                    .then(() => this.isLoading = false);
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

        },

        watch: {

            // Whenever a note is clicked on (i.e., "opened") on the note
            // card, its tasks are fetched and shown on the task card. Echo
            // stops listening to the channel for the previously opened note
            // and starts listening for the currently-open note instead.
            openNoteId(newId, oldId) {

                // The default value of the ID is zero which is a placeholder.
                if (this.openNoteId > 0) {
                    this.isLoading = true;
                    this.fetchTasks(this.openNoteId);
                }

                // Stops listening to the old note's channel.
                Echo.leave(`note.${ oldId }`);

                // Starts listening to the current note's channel.
                Echo.channel(`note.${ newId }`)
                    .listen('TaskUpdated', (data) => {

                        this.tasks = data.tasks;

                        // Notification is cleared (if any).
                        this.$emit('notification', {});

                    });

            }

        }

    }

</script>