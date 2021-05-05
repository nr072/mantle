<template>

    <li class="task px-5 py-2" :class="{ highlighted: isEditing }">

        <div v-show="!isEditing" class="level mb-0">

            <div class="level-left">

                <input type="checkbox" v-model="isDone"
                    class="mr-2" 
                    @change="toggleStatus"
                >

                <div>
                    <div class="task-name" :class="{ done: task.isDone }">{{ task.name }}</div>
                    <div v-if="task.dueTimeUtc"
                        class="task-lefttime" :class="{ done: task.isDone }"
                    >{{ leftTime }}</div>
                </div>

            </div>

            <div class="level-right">

                <div class="buttons">

                    <button v-show="!isEditing"
                        class="button is-small is-info is-light"
                        @click="showEditSection"
                    >Edit</button>

                    <button v-show="!isEditing"
                        class="button is-small is-danger is-light"
                        @click="$emit('task-removal', task.id);"
                    >Remove</button>

                </div>

            </div>

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





<style scoped>

    .task {
        list-style: none;
        padding-top: 0.75rem;
        margin-left: -1.5rem;
        margin-right: -1.5rem;
    }
    .task.highlighted {
        background: beige;
    }

    .task > .level {
        margin-bottom: 0.75rem;
    }

    .task-name.done,
    .task-lefttime.done {
        text-decoration: line-through;
    }

</style>





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
                isEditing: false,

                isDone: this.task.isDone

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
            },

            // A task's status (done or not) is toggled.
            toggleStatus() {
                const data = {
                    id: this.task.id,
                    isDone: this.isDone
                };
                this.$emit('task-update', data);
            }

        }

    }

</script>