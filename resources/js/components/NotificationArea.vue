<template>
    <section>

        <div v-if="message.canShow">
            <span>{{ message.type }}: {{ message.content }}</span>
            <button
                @click="$emit('notification-clear', {})"
                title="Dismiss notification"
            >&times;</button>
        </div>

    </section>
</template>





<style scoped>

    section {
        text-align: right;
    }

</style>





<script>
    export default {

        props: [ "message" ]

    }
</script>