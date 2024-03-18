<template>
    <div>
        <div class="d-flex flex-row justify-content-between">
            <router-link :to="{ name: 'home.index' }">Home</router-link>
            <router-link v-if="!token" :to="{ name: 'user.login' }">Login</router-link>
            <router-link v-if="!token" :to="{ name: 'user.registration' }">Registration</router-link>
            <router-link v-if="token && hasStore" :to="{ name: 'user.store' }">Store</router-link>
            <router-link v-if="token && isAdmin" :to="{ name: 'user.admin' }">Admin</router-link>
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
            token: null,
            hasStore: false,
            isAdmin: false
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
            if (this.token) {
                axios.get('api/user/check/store').then(res => {
                    this.hasStore = res.data.result;
                    localStorage.setItem('isStore', this.hasStore)
                }).catch(err => {
                    console.log(err)
                })

                axios.get('api/user/check/admin').then(res => {
                    this.isAdmin = res.data.result;
                    localStorage.setItem('isAdmin', this.isAdmin)
                }).catch(err => {
                    console.log(err)
                })
            }
        },

        logout() {
            axios.post('/logout').then( r => {
                localStorage.removeItem('x-xsrf-token')
                localStorage.removeItem('isStore')
                localStorage.removeItem('isAdmin')
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
