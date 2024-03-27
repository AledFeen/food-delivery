<script>
export default {
    name: "Courier panel",

    mounted() {
        this.getCourier()
    },

    methods: {
        getCourier() {
            axios.get('/api/user/courier').then(res => {
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
    <div class="text-light">Courier panel</div>
</template>

<style scoped>

</style>
