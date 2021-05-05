<template>
    <div>

        <div class="field">
            <div class="control">
                <input ref="newNameInput"
                    type="text" v-model.trim="newName"
                    class="input is-small"
                    @keyup.enter="updateTask"
                    required 
                >
            </div>
        </div>

        <div class="field is-horizontal is-justify-content-space-between">

            <div class="field has-addons mb-0">

                <div class="control">
                    <input type="date" v-model="newDate"
                        class="input is-small"
                        :min="newDateMinValue"
                        :max="newDateMaxValue"
                    >
                </div>
                <div class="control">
                    <input type="text" v-model.trim="newTime"
                        class="input is-small new-time-input"
                        @keyup.enter="updateTask"
                        placeholder="23:01"
                    >
                </div>

                <div class="control">
                    <button v-show="currentDueTime"
                        class="button is-small is-outlined is-danger"
                        @click="resetInputs(); $emit('due-time-removal');"
                    >Remove time</button>
                </div>

            </div>

            <div class="field">

                <div class="buttons is-right">
                    <button class="button is-small is-success"
                        @click="updateTask"
                    >Save</button>
                    <button class="button is-small is-danger"
                        @click="resetInputs(); $emit('edit-cancellation');"
                    >Cancel</button>
                </div>

            </div>

        </div>

    </div>
</template>





<style scoped>

    .new-time-input {
        width: 50px;
    }

</style>





<script>
    
    export default {

        props: {

            // When this component is used as a task editor, existing
            // task name - and, optionally, due time - will be passed.
            currentName: String,
            currentDueTime: Date,

            newDateMinValue: String,
            newDateMaxValue: String

        },

        data() {
            return {

                // Input value for new name.
                newName: '',

                // New 'due time' input is made up of 2 parts: a date
                // input and a time input.
                newDate: '',
                newTime: ''

            }
        },

        computed: {

            // Date needs to be in 'yyyy-mm-dd' format to be used as
            // pre-fill for the date <input> element.
            paddedDateString() {

                if (!this.currentDueTime) {
                    return;
                }

                const [,, date, year] = this.currentDueTime.toString().split(' ');
                const m = this.currentDueTime.getMonth() + 1;
                const month = m < 10 ? ('0' + m) : m;

                return (year + '-' + month + '-' + date);

            },

            // Time needs to be in 'hh:mm' format for no specific reason.
            // But it must be in 24-hour format.
            trimmedTimeString() {
                if (!this.currentDueTime) {
                    return;
                }
                return this.currentDueTime.toTimeString().slice(0, 5);
            }

        },

        mounted() {

            // If this is component is used as a task adder, the default
            // values will be those passed from the parent component. And
            // if this is used as a task editor, there will be no default
            // value.
            this.resetInputs();

        },

        methods: {

            // If there are new values, the new data is passed up to the
            // parent component for update, and input values are reset.
            updateTask() {

                // A task must have a name.
                if (!this.newName) {
                    return;
                }

                // Properties are added based on whether they have new
                // values or not.
                const data = {};

                // An update will occur only if the value changed.
                if (this.currentName !== this.newName) {
                    data.name = this.newName;
                }

                const newDueTime = new Date(this.newDate + ' ' + this.newTime);

                // An update will occur only if there is a valid due time
                // input and:
                //   - there is no existing due time,
                //   - or there is an existing due time and the new input
                //     is not the same as the existing one.
                if (newDueTime.getTime()) {
                    if (
                        !this.currentDueTime
                        || this.currentDueTime.getTime() !== newDueTime.getTime()
                    ) {
                        data.dueTime = newDueTime.toISOString().split('Z')[0];
                    }
                }

                // Data is passed up only if there is a new value.
                if (Object.keys(data).length) {
                    this.$emit('task-update', data);
                }

                // Inputs are reset (old values if task editor, empty if
                // task adder).
                this.resetInputs();

            },

            // Name, date and time inputs are reset: If this is a task
            // editor, the inputs are reset to their passed values; and
            // if this is a task adder, the inputs are just cleared.
            resetInputs() {
                this.newName = this.currentName || '';
                if (this.currentDueTime) {
                    this.newDate = this.paddedDateString;
                    this.newTime = this.trimmedTimeString;
                } else {
                    this.newDate = '';
                    this.newTime = '';
                }
            }

        },

        watch: {

            // Whenever the name or the due time is changed by the parent
            // component, the inputs values are reset so that the input
            // fields do not show old values as pre-fills.
            currentName: function() {
                this.resetInputs();
            },
            currentDueTime: function() {
                this.resetInputs();
            }

        }

    }

</script>