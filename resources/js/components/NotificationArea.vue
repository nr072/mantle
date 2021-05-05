<template>
    <div v-show="content" class="columns">
        <div class="column is-half is-offset-half">

            <div class="notification" :class="classObject">

                <button class="delete" @click="$emit('close')"></button>

                <p>{{ content }}</p>

            </div>

        </div>
    </div>
</template>





<script>

    export default {

        props: {

            type: String,
            content: String

        },

        computed: {

            classObject() {
                return {
                    'is-danger': this.type === 'error'
                };
            }

        }

    }

</script>