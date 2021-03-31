<template>
    <div>

        <span>{{ task.name }}</span>
        <small :title="niceDueTime">{{ timeLeftString }}</small>

        <button :class="{ hidden: isSettingDueTime }"
            @click="isSettingDueTime = true"
        >Set due time</button>

        <div :class="{ hidden: !isSettingDueTime }">
            
            <input type="date"
                :min="now.toISOString().split('T')[0]"
                :max="nowPlus1Year.toISOString().split('T')[0]"
                v-model="dateInput"
            >
            <input type="text" class="time-input"
                placeholder="23:01"
                v-model="timeInput"
                @keyup.enter="setDueTime"
            >

            <button
                @click="setDueTime"
                :disabled="!canShowDTIConfirmBtn"
            >Confirm</button>

            <button class="btn"
                @click="isSettingDueTime = false; clearDateTimeInputs();"
            >Cancel</button>

        </div>

        <button
            :class="{ hidden: this.task.due_time === null }"
            @click="removeDueTime"
        >Remove due time</button>

    </div>
</template>





<style scoped>
    
    .time-input {
        max-width: 50px;
    }

</style>





<script>

    export default {

        props: [ "task" ],

        mounted() {

            // The amount of time left is calculated using a 'setInterval'
            // call.
            this.keepCalculatingLeftTime();

        },

        data() {
            return {

                // Different parts (days, hours, minutes) of the 'left time'
                // are computed properties that depend on this variable. So,
                // this is updated regularly which, in turn, makes Vue update
                // those properties.
                now: new Date(),

                // The interval ID is saved so that the 'setInterval' instance
                // can be cleared when it is past due time.
                iid: 0,

                // Some elements behave differently based on whether the
                // user is setting due time or not. (For example, usually,
                // the inputs are hidden; but when they are revealed, the
                // button that revealed them gets hidden.
                isSettingDueTime: false,

                // The date and the time for the new due time are obtained
                // as different inputs from the user. They are later merged.
                dateInput: "",
                timeInput: ""

            }
        },

        computed: {

            secondsLeft() {

                if (this.task.due_time === null) {
                    return 0;
                }

                // The number of seconds left until due time is calculated
                // so that it can be used to figure out the numbers of
                // days, hours, and minutes left.
                const now = new Date();
                const dueTime = new Date(this.task.due_time);
                const diffInSeconds = (dueTime - now) / 1000;

                return diffInSeconds > 1 ? diffInSeconds : 0;

            },

            daysLeft() {
                return (
                    this.secondsLeft >= 60
                        ? parseInt(this.secondsLeft / 86400)
                        : 0
                );
            },

            hoursLeft() {
                return (
                    this.secondsLeft >= 60
                        ? parseInt( (this.secondsLeft % 86400) / 3600 )
                        : 0
                );
            },

            minutesLeft() {
                return (
                    this.secondsLeft >= 60
                        ? parseInt( (this.secondsLeft % 3600) / 60)
                        : 0
                );
            },

            // The amount of time left is presented in '9d 9h 9m left'
            // format.
            timeLeftString() {

                if (this.task.due_time === null) {
                    return "";
                }

                if (this.secondsLeft <= 0) {
                    return "(past)";
                }

                let timeLeftString = (
                    ( this.daysLeft > 0 ? this.daysLeft + "d " : "" ) +
                    ( this.hoursLeft > 0 ? this.hoursLeft + "h " : "" ) +
                    ( this.minutesLeft > 0 ? this.minutesLeft + "m " : "" )
                );

                return ( "(" + timeLeftString.trim() + " left)" );

            },

            // Raw due time in a comparatively good-looking format.
            niceDueTime() {
                return (
                    this.task.due_time !== null
                        ? new Date(this.task.due_time).toLocaleString()
                        : ""
                );
            },

            // A date 365 days later is used to set the maximum limit
            // for the due time's date input.
            nowPlus1Year() {
                return new Date( this.now.getTime() + (3600000 * 24 * 365) );
            },

            // The confirm button with the date-time setters can be shown
            // only when the inputs are revealed and both the date and the
            // time input have a value.
            canShowDTIConfirmBtn() {
                return (
                    this.isSettingDueTime
                    && this.dateInput !== ""
                    && this.timeInput.trim() !== ""
                );
            }

        },

        methods: {

            // If this task has a valid due time, its 'left time' is
            // calculated at a regular interval.
            keepCalculatingLeftTime() {

                if (this.task.due_time === null) {
                    return;
                }

                // 'Left time' is calculated once first so that it is
                // shown at once at page load.
                this.calculateLeftTime();

                // The interval ID is used to clear the 'setInterval'
                // instance when it is past due time.
                this.iid = setInterval(this.calculateLeftTime, 60000);

            },



            // At a regular interval, the current time is updated, which
            // prompts Vue to update the 'left time'. When due time is
            // past (or very close to it), the 'setInterval' instance is
            // cleared using the saved interval ID.
            calculateLeftTime() {
                this.now = new Date();
                if (this.secondsLeft < 60 && this.iid > 0) {
                    clearInterval(this.iid);
                }
            },



            // If a valid date-time is entered, the task's due time is
            // updated (the parent component actually does the update),
            // and the input fields are cleared and hidden again.
            setDueTime() {

                if (this.dateInput === "" || this.timeInput.trim() === "") {
                    alert("Date-time not provided");
                    return;
                }

                const newDueTime = this.dateInput + " " + this.timeInput.trim();
                if ( isNaN( new Date(newDueTime).getTime() ) ) {
                    alert("Invalid date-time input");
                    return;
                }

                // A custom event is emitted so that the parent component
                // can update the due time.
                this.$emit("due-time-update", {
                    id: this.task.id,
                    due_time: newDueTime
                });

                // Input fields are cleared and hidden.
                this.clearDateTimeInputs();
                this.isSettingDueTime = false;

            },



            // Removing due time works the same way as updating it, except
            // the new value is 'null' in this case.
            removeDueTime() {
                this.$emit('due-time-update', {
                    id: this.task.id,
                    due_time: null
                });
            },



            // Due time setter input fields are cleared.
            clearDateTimeInputs() {
                this.dateInput = "";
                this.timeInput = "";
            }

        }

    }

</script>