<template>
    <div>

        <notification-area
            :type="notification.type"
            :content="notification.content"
            @close="notification = {}"
        ></notification-area>

        <task-list
            @notification="notification = $event"
        ></task-list>

    </div>
</template>





<script>

    import NotificationArea from './NotificationArea.vue';
    import TaskList from './TaskList.vue';
    
    export default {

        components: {

            NotificationArea,
            TaskList

        },

        data() {
            return {

                notification: {
                    type: '',
                    content: ''
                }

            }
        }

    }

</script>