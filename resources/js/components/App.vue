<template>
    <section class="section pt-5">

        <notification-area
            :type="notification.type"
            :content="notification.content"
            @close="notification = {}"
        ></notification-area>

        <div class="columns">

            <div class="column is-half">
                <div class="card has-background-white-bis">

                    <header class="card-header">
                        <p class="card-header-title">[list name]</p>
                    </header>

                    <div class="card-content">

                        <task-list
                            @notification="notification = $event"
                            :newDateMinValue="newDateMinValue"
                            :newDateMaxValue="newDateMaxValue"
                        ></task-list>

                        <div class="mt-5">

                            <button v-show="!isAddingTask"
                                class="button is-small is-fullwidth is-info is-outlined"
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

                    </div>

                </div>
            </div>
            
        </div>

    </section>
</template>





<script>

    import NotificationArea from './NotificationArea.vue';
    import TaskList from './TaskList.vue';
    import TaskAdder from './TaskEditor.vue';
    
    export default {

        components: {

            NotificationArea,
            TaskList,
            TaskAdder // Used as both an editor and an adder

        },

        data() {
            return {

                notification: {
                    type: '',
                    content: ''
                },

                // Used to show/hide the task adder section.
                isAddingTask: false,

                // Today's date is used to set both a minimum value and
                // a maximum value for setting a due time. This value is
                // not reactive. It is used simply for computed values
                // that are passed to all task editor components.
                now: new Date()

            }
        },

        computed: {

            // Today's date and a date from 1 year ahead are used to set
            // the minimum and the maximum values for setting due time.
            // Since these values are unlikely to change much in a single
            // session (before the user closes the browser tab), these
            // are passed to all task editor components.
            newDateMinValue() {
                return this.now.toISOString().split('T')[0];
            },
            newDateMaxValue() {
                const nowPlus1Year = new Date(this.now.getTime() + 31536000000);
                return nowPlus1Year.toISOString().split('T')[0];
            }

        },

        methods: {

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
            }

        }

    }

</script>