<script>
export default {
    name: "Admin panel",

    mounted() {
        this.getStore()
    },

    methods: {
        getStore() {
            axios.get('/api/user/admin').then(res => {
                if(res.data.response === false) {
                    window.alert("У вас немає доступу до цієї сторінки");
                    this.$router.push({name: 'home.index'})
                } else {
                    console.log(res.data)
                }
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>

<template>
    <div class="text-light">Admin panel</div>
    <router-link :to="{ name: 'admin.users' }">User list</router-link>
</template>



<style scoped>

</style>
