<template>

    <p v-if="isLoading">Loading ...</p>

    <ul v-else-if="notes.length">

        <li v-for="note of notes">
            <button class="button"
                @click="$emit('note-opened', note.id)"
            >#{{ note.id }} {{ note.name }}</button>
        </li>

    </ul>

    <p v-else>No note found</p>

</template>





<script>
    
    export default {

        data() {
            return {

                notes: [],

                // A "loading" message may need to be shown in some cases
                // (e.g., fetching data).
                isLoading: false

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
            }

        }

    }

</script>