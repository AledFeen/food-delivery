<script>
export default {
    name: "My store",

    mounted() {

    },

    methods: {

    }
}
</script>

<template>
    <div class="text-light">My store</div>
    <router-link :to="{ name: 'store.profile' }">Profile</router-link>
    <router-link :to="{ name: 'store.panel' }">Panel</router-link>
</template>

<style scoped>

</style>
