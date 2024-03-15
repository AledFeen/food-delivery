<template>
    <div>
        <div class="d-flex flex-row justify-content-between">
            <router-link v-if="token" :to="{ name: 'home.index' }">Home</router-link>
            <router-link v-if="!token" :to="{ name: 'user.login' }">Login</router-link>
            <router-link v-if="!token" :to="{ name: 'user.registration' }">Registration</router-link>
            <input v-if="token" @click.prevent="logout" type="submit" value="logout" class="btn btn-primary">
        </div>
        <router-view :key="$route.fullPath"></router-view>
    </div>
</template>

<script>
export default {
    name: "Index",

    data() {
        return {
            token: null
        }
    },

    mounted() {
        this.getToken()
    },

    updated() {
        this.getToken()
    },

    methods: {
        getToken() {
            this.token = localStorage.getItem('x-xsrf-token')
        },

        logout() {
            axios.post('/logout')  .then( r => {
                localStorage.removeItem('x-xsrf-token')
                console.log(r)
            }).then(res => {
                this.$router.push({name: 'user.login'})
            }).catch(err => {
                console.log(err.response)
            })
        }
    }
}
</script>

<style scoped>


</style>
