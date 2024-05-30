<script>
export default {
    name: "Index",

    data() {
        return {
            token: null,
            hasStore: null,
            isAdmin: null,
            isCourier: null
        }
    },

    mounted() {
        this.getToken()
    },

    watch: {
        $route(to, from) {
            this.getToken()
        }
    },

    methods: {
        getToken() {
            this.token = localStorage.getItem('x-xsrf-token')
            if (this.token) {
                axios.get('/api/user/check/store').then(res => {
                    this.hasStore = res.data.result;
                    localStorage.setItem('isStore', this.hasStore)
                }).catch(err => {
                    console.log(err)
                })

                axios.get('/api/user/check/admin').then(res => {
                    this.isAdmin = res.data.result;
                    localStorage.setItem('isAdmin', this.isAdmin)
                }).catch(err => {
                    console.log(err)
                })

                axios.get('/api/user/check/courier').then(res => {
                    this.isCourier = res.data.result;
                    localStorage.setItem('isCourier', this.isCourier)
                }).catch(err => {
                    console.log(err)
                })
            }
        },

        logout() {
            axios.post('/logout').then(r => {
                localStorage.removeItem('x-xsrf-token')
                localStorage.removeItem('isStore')
                localStorage.removeItem('isAdmin')
                localStorage.removeItem('isCourier')
            }).then(res => {
                this.$router.push({name: 'user.login'})
            }).catch(err => {
                console.log(err.response)
            })
        }
    }
}
</script>

<template>
    <div class="d-flex flex-column" style="min-height: 100vh;">
        <header class="pt-4 pb-4 bg-orange">
            <div class="container">
                <div class="d-flex flex-row justify-content-between">
                    <router-link :to="{ name: 'home.index' }" class="btn header-link">Home</router-link>
                    <router-link v-if="!token" :to="{ name: 'user.login' }" class="btn header-link">Login</router-link>
                    <router-link v-if="!token" :to="{ name: 'user.registration' }" class="btn header-link">Registration</router-link>
                    <template v-if="token && hasStore">
                        <div class="dropdown">
                            <a class="btn dropdown-toggle header-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Store
                            </a>
                            <ul class="dropdown-menu text-center">
                                <li><router-link :to="{ name: 'store.profile' }" class="btn header-link">Profile</router-link></li>
                                <li><router-link :to="{ name: 'store.panel' }" class="btn header-link">Panel</router-link></li>
                            </ul>
                        </div>
                    </template>
                    <template v-if="token && isAdmin">
                        <div class="dropdown">
                            <a class="btn dropdown-toggle header-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin
                            </a>
                            <ul class="dropdown-menu text-center">
                                <router-link :to="{ name: 'admin.users' }" class="btn header-link">User list</router-link>
                            </ul>
                        </div>
                    </template>
                    <router-link v-if="token && isCourier" :to="{ name: 'user.courier' }" class="btn header-link">Courier</router-link>
                    <router-link v-if="token" :to="{ name: 'orders' }" class="btn header-link">My orders</router-link>
                    <input v-if="token" @click.prevent="logout" type="submit" value="Logout"  class="btn header-link">
                </div>
            </div>
        </header>
        <main class="main container flex-grow-1 bg-light">
            <router-view :key="$route.fullPath"></router-view>
        </main>
        <footer class="pt-5 pb-5 bg-black text-light">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h5>Join Us</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="footer-link">For Partners</a></li>
                            <li><a href="#" class="footer-link">Couriers</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 text-center">
                        <h5>Useful Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="footer-link">About Us</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 text-center">
                        <h5>Contact Us</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="footer-link">Social link</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 text-center">
                        <h5>Terms & Conditions</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="footer-link">Privacy Policy</a></li>
                            <li><a href="#" class="footer-link">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
    .bg-orange {
        background-color: #fd974f;
    }

    .footer-link {
        color: lightgray;
        text-decoration: none;
    }

    .footer-link:hover {
        color: #f8fafc;
        text-decoration: underline;
    }

    .header-link {
        font-size: 16px;
        text-decoration: none;
        color: rgba(var(--bs-dark-rgb));
        transition: background-color 0.3s ease;
    }

    .header-link:hover {
        color: #f8fafc;
        background-color: #805a3b;
    }


</style>
