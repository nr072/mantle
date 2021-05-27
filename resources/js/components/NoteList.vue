<template>

    <p v-if="isLoading">Loading ...</p>

    <ul v-else-if="notes.length">

        <li v-for="note of notes" class="is-flex is-align-items-center mb-2">

            <button class="button is-small is-text note-name"
                :class="openNoteId === note.id && 'is-active'"
                @click="openNoteId = note.id; $emit('note-opened', note.id)"
            >{{ note.name }}</button>

            <span v-if="note.numOfPendingTasks"
                class="tag is-small is-light"
                :title="note.numOfPendingTasks + ' task' + (note.numOfPendingTasks > 1 ? 's' : '') + ' (pending)'"
            >{{ note.numOfPendingTasks }}</span>

        </li>

    </ul>

    <p v-else>No note found</p>

</template>





<style scoped>

    .note-name.is-text {
        text-decoration: none;
    }
    .note-name.is-text:hover,
    .note-name.is-text.is-hovered,
    .note-name.is-text:focus,
    .note-name.is-text.is-focused,
    .note-name.is-text:active,
    .note-name.is-text.is-active {
        color: var(--color-1);
        background-color: transparent;
    }

</style>





<script>
    
    export default {

        data() {
            return {

                notes: [],

                // A "loading" message may need to be shown in some cases
                // (e.g., fetching data).
                isLoading: false,

                // Used to show the currently-open note name as highlighted.
                // Default value is just a placeholder.
                openNoteId: 0

            }
        },

        created() {

            // Notes are fetched on load and fetched data is passed up
            // so that available note names can be shown in a dropdown
            // on the task card for creating new tasks.
            this.isLoading = true;
            this.fetchNotes(() => {
                this.$emit('notes-fetched', this.notes);
            });

            // When the status of a task changes, the "NoT" badge beside
            // that task's note name on the note card is updated.
            Echo.channel('notes')
                .listen('TaskStatusChanged', (note) => {
                    if(note && Object.keys(note).length) {
                        this.updateNotBadgeNumber(note);
                    }
                });

            // The note list is updated whenever a new note is created.
            Echo.channel('notes')
                .listen('NoteCreated', (data) => {
                    if (data && data.notes) {
                        this.notes = data.notes;
                    }
                });

        },

        methods: {

            fetchNotes(callback) {
                axios.get('/api/notes')
                    .then(response => {
                        if (response.status === 200) {
                            this.notes = response.data;
                        }
                    })
                    .then(() => {
                        callback && callback();
                    })
                    .catch(error => this.$emit('notification', {
                        type: 'error',
                        content: error.message
                    }))
                    .then(() => this.isLoading = false);
            },

            // Updates the numbers of pending and done tasks in a "numbers
            // of tasks" or "NoT" badge.
            // One or both numbers are passed, and the matching note from
            // the reactive "notes" array is updated accordingly.
            updateNotBadgeNumber(updatedNote) {

                if (
                    !updatedNote.id
                    || (!updatedNote.numOfPendingTasks && !updatedNote.numOfDoneTasks)
                ) {
                    return;
                }

                const length = this.notes.length;
                for (let i = 0; i < length; ++i) {
                    const note = this.notes[i];

                    // The matching note's number(s) is (are) updated.
                    if (note.id === updatedNote.id) {
                        if (updatedNote.numOfPendingTasks) {
                            note.numOfPendingTasks = updatedNote.numOfPendingTasks;
                        }
                        if (updatedNote.numOfDoneTasks) {
                            note.numOfDoneTasks = updatedNote.numOfDoneTasks;
                        }
                        break;
                    }

                }

            }

        }

    }

</script>