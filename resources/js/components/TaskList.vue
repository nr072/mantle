<template>
    <div>

        <section v-if="tasks.length > 0">
            <h1>Tasks</h1>
            <ul>
                <li v-for="task in tasks" :id="task.id">#{{ task.id }} {{ task.name }}</li>
            </ul>
        </section>

        <p v-else>No task found</p>

    </div>
</template>





<script>
export default {

    data() {
        return {
            tasks: [],
            error: ""
        }
    },

    mounted() {

        this.fetchTasks();

    },

    methods: {

        fetchTasks() {
            axios.get("/api/tasks")
                .then(response => {
                    if (response.status === 200) {
                        this.tasks = response.data;
                    }
                })
                .catch(error => {
                    this.error = error;
                    alert(error);
                });
        }

    }

}
</script>