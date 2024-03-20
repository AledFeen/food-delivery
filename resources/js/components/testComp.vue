<template>
    <div class="text-light">Admin panel</div>
    <div class="container-md">
        <h1 class="mb-4 text-light">Users</h1>
        <div v-if="users === null" class="text-light">Loading...</div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Created at</th>
                <th scope="col"></th>
            </tr>
            </thead>
        </table>
    </div>
</template>

<script>
export default {
    name: "Test panel",

    mounted() {
        this.getStore()
    },

    methods: {
        getStore() {
        }
    }
}
</script>

<style scoped>

</style>
