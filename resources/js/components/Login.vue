<script>
export default {
    name: "Login",

    data() {
        return {
            email: null,
            password: null
        }
    },

    methods: {
        login() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                // Login...
                axios.post('login', { email: this.email, password: this.password }).then( r => {
                    localStorage.setItem('x-xsrf-token', r.config.headers['X-XSRF-TOKEN'])
                    this.$router.push({name: 'home.index'})
                }).catch(error => {
                    alert(error.response.data.message)
                });
            });
        },
    }
}
</script>

<template>
    <div class="w-25 mx-auto mt-5 mb-5">
        <input v-model="email" type="email" placeholder="email" class="form-control mt-3 mb-3">
        <input v-model="password" type="password" placeholder="password" class="form-control mb-3">
        <input @click.prevent="login" type="submit" value="login" class="btn btn-primary">
    </div>
</template>

<style scoped>

</style>

