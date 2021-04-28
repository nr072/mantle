<template>

    <li>

        <div v-show="!isEditing">

            {{ task.name }}
            {{ task.dueTimeUtc && leftTime }}

            <button v-show="!isEditing"
                @click="showEditSection"
            >Edit</button>

            <button v-show="!isEditing"
                @click="$emit('task-removal', task.id);"
            >Remove</button>

        </div>

        <task-editor v-show="isEditing"
            ref="taskEditorComp"
            :currentName="task.name"
            :currentDueTime="dueTime"
            :newDateMinValue="newDateMinValue"
            :newDateMaxValue="newDateMaxValue"
            @task-update="updateTask"
            @due-time-removal="removeDueTime"
            @edit-cancellation="isEditing = false"
        ></task-editor>

    </li>

</template>





<script>

    import TaskEditor from './TaskEditor.vue';

    export default {

        props: {

            task: Object,
            newDateMinValue: String,
            newDateMaxValue: String

        },

        components: {

            TaskEditor

        },

        data() {
            return {

                // Used to show/hide the section for editing a task.
                isEditing: false

            }
        },

        computed: {

            // Due time received from API is actually a string which needs
            // to be converted to a date object first.
            dueTime() {
                if (this.task.dueTimeUtc) {
                    return new Date(this.task.dueTimeUtc + ' UTC');
                }
            },

            // TODO
            leftTime() {
                if (this.task.dueTimeUtc) {
                    return '(' + this.dueTime.toLocaleString() + ')';
                }
            }

        },

        methods: {

            // When the edit section is shown, input fields are pre-filled
            // with the current task name and the due time (if any).
            showEditSection() {

                this.isEditing = true;

                // The name input field is focused for better UX.
                this.$nextTick(() => {
                    this.$refs.taskEditorComp.$refs.newNameInput.focus();
                });

            },

            // The task ID is set while passing data to the parent component
            // (since the editor component does not have the ID), and the
            // edit section is hidden again. The task list parent component
            // does the actual update.
            updateTask(data) {
                if (Object.keys(data).length) {
                    data.id = this.task.id;
                    this.$emit('task-update', data);
                }
                this.isEditing = false;
            },

            // Due time is removed by setting 'null' as the new value.
            removeDueTime() {
                this.$emit('task-update', {
                    id: this.task.id,
                    dueTime: null
                });
                this.isEditing = false;
            }

        }

    }

</script>