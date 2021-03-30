<template>
    <div>

        <span>{{ task.name }}</span>
        <small :title="niceDueTime">{{ timeLeftString }}</small>

    </div>
</template>





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
                iid: 0

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
            }

        }

    }

</script>