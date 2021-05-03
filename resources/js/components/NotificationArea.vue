<template>
    <section v-show="content">

        <span v-show="type">{{ type }}:</span> {{ content }}
        <button @click="$emit('close')">&times;</button>

    </section>
</template>





<script>

    export default {

        props: {

            type: String,
            content: String

        }

    }

</script>