<template>
    <div class="mb-3">

        <button v-show="!isAddingNote"
            class="button is-small is-fullwidth is-info is-outlined"
            @click="showAdder"
        >Add note</button>

        <div v-show="isAddingNote">
            <div class="field">
                <input ref="newNameInput"
                    type="text" v-model="name"
                    class="input is-small"
                    @keyup.enter="addNote"
                    required
                    placeholder="Type new note name"
                >
            </div>

            <div class="buttons is-right field">
                <button class="button is-small is-success"
                    @click="addNote"
                >Save</button>

                <button class="button is-small is-danger"
                    @click="name = ''; isAddingNote = false"
                >Cancel</button>
            </div>
        </div>

    </div>
</template>





<script>

    export default {

        data() {
            return {

                // Used to show/hide the note adder section.
                isAddingNote: false,

                // New note name (that the user will type).
                name

            }
        },

        methods: {

            // Shows adder-related field and buttons.
            showAdder() {
                this.isAddingNote = true;
                this.$nextTick(() => this.$refs.newNameInput.focus());
            },

            // A new note is created, and the adder section is hidden again.
            addNote() {
                const url = '/api/notes';
                const data = {
                    name: this.name
                };
                axios.post(url, data)
                    .catch(error => this.$emit('notification', {
                        type: 'error',
                        content: error.message
                    }))
                    .then(() => {
                        this.isAddingNote = false;
                        this.name = '';
                    });
            }

        }

    }

</script>