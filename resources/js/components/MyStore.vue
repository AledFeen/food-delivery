<template>
    <div class="text-light">My store</div>
</template>

<script>
export default {
    name: "My store",

    mounted() {
        //this.getStore()
    },

    methods: {
        getStore() {
            axios.get('/api/user/store').then(res => {

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

<style scoped>

</style>
