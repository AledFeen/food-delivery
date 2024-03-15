<template>
    <div class="w-25">
        <input v-model="name" type="text" placeholder="name" class="form-control mt-3 mb-3">
        <input v-model="email" type="email" placeholder="email" class="form-control mt-3 mb-3">
        <input v-model="password" type="password" placeholder="password" class="form-control mb-3">
        <input v-model="password_confirmation" type="password" placeholder="password" class="form-control mb-3">
        <input @click.prevent="register" type="submit" value="registration" class="btn btn-primary">
    </div>
</template>

<script>
export default {
    name: "Registration",

    data() {
        return {
            name:null,
            email: null,
            password: null,
            password_confirmation: null
        }
    },

    methods: {
        register() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                }).then(r => {
                    localStorage.setItem('x-xsrf-token', r.config.headers['X-XSRF-TOKEN'])
                    this.$router.push({name: 'home.index'})
                }).catch(err => {
                    console.log(err.response)
                })
            })
        }
    }
}
</script>

<style scoped>

</style>
