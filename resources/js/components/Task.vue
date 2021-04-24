<template>

    <li>

        <div v-show="!isEditing">

            {{ task.name }}
            {{ task.dueTime && leftTime }}

            <button v-show="!isEditing"
                @click="showEditSection"
            >Edit</button>

            <button v-show="!isEditing"
                @click="$emit('task-removal', task.id)"
            >Remove</button>

        </div>

        <div v-show="isEditing">

            <input ref="newNameInput"
                v-model.trim="newName" type="text"
                @keyup.enter="updateTask"
            >

            <input v-model="newDate" type="date"
                :min="newDateMinValue"
                :max="newDateMaxValue"
            >
            <input class="new-time-input"
                v-model.trim="newTime" type="text"
                @keyup.enter="updateTask"
                placeholder="23:01"
            >

            <button v-if="task.dueTime" @click="removeDueTime">Remove time</button>

            <div>
                <button @click="updateTask">Save</button>
                <button @click="clearInputs(); isEditing = false;">Cancel</button>
            </div>

        </div>

    </li>

</template>





<style scoped>

    .new-time-input {
        width: 50px;
    }

</style>





<script>
    export default {

        props: {
            task: Object
        },

        data() {
            return {

                // Sections are shown/hidden based on whether a task is
                // being edited or not.
                isEditing: false,

                // Input value for task name when a task is edited.
                newName: '',

                // New 'due time' input is made up of 2 parts: a date
                // input and a time input.
                newDate: '',
                newTime: '',

                // Today's date is used to set both a minimum value and
                // a maximum value for setting due time.
                now: new Date()

            }
        },

        computed: {

            // 'Due time' is divided into a date and a time part so that
            // they can be used in <input> elements (to show pre-filled
            // inputs when a task is being edited). For time, only the
            // 'hh:mm' part is kept.
            dividedDueTime() {
                if (!this.task.dueTime) {
                    return;
                }
                const parts = new Date(this.task.dueTime).toISOString().split('T');
                return {
                    date: parts[0],
                    time: parts[1].slice(0, 5)
                };
            },



            // TODO
            leftTime() {
                if (this.task.dueTime) {
                    return '(' + this.dividedDueTime.date + ' ' + this.dividedDueTime.time + ')';
                }
            },



            // Today's date and a date from 1 year ahead are used to the
            // minimum and the maximum values for setting due time.
            newDateMinValue() {
                return this.now.toISOString().split('T')[0];
            },
            newDateMaxValue() {
                const nowPlus1Year = new Date(this.now.getTime() + 31536000000);
                return nowPlus1Year.toISOString().split('T')[0];
            }

        },

        methods: {

            // When the edit section is shown, input fields are pre-filled
            // with the current task name and the due time.
            showEditSection() {

                this.isEditing = true;

                // The task name input field is pre-filled with the current
                // name.
                this.newName = this.task.name;

                // If a task has a due time, the date and the time inputs
                // are pre-filled.
                if (this.task.dueTime) {
                    this.newDate = this.dividedDueTime.date;
                    this.newTime = this.dividedDueTime.time;
                }

                // The name input field is focused for better UX.
                this.$nextTick(() => this.$refs.newNameInput.focus());

            },



            // Data to be updated is passed to the task list component
            // so that it can do the actual updating and then fetch
            // tasks again.
            updateTask() {

                const data = {};

                // Only new values are sent to the back-end for update.
                if (this.task.name.trim() !== this.newName) {
                    data.name = this.newName;
                }
                const newDueTime = new Date(this.newDate + ' ' + this.newTime);
                if (newDueTime.getTime()) {
                    data.dueTime = this.newDate + 'T' + (this.newTime || '00:00');
                }

                // If there is at least 1 new value, an event is emitted
                // along with new values so that the task list component
                // can do the actual update.
                if (Object.keys(data).length) {
                    data.id = this.task.id;
                    this.$emit('task-update', data);
                }

                // The edit section is hidden.
                this.isEditing = false;

            },



            // Due time is removed by sending 'null' as the new value.
            removeDueTime() {

                this.$emit('task-update', {
                    id: this.task.id,
                    dueTime: null
                });

                this.clearInputs();

                // The edit section is hidden.
                this.isEditing = false;

            },



            // Name, date and time inputs are cleared.
            clearInputs() {
                this.newName = '';
                this.newDate = '';
                this.newTime = '';
            }

        }

    }
</script>