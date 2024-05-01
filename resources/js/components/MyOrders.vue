<script>
export default {
    name: "Orders",

    data() {
        return {
            orders: null
        }
    },

    mounted() {
        this.getUserOrders()
    },

    methods: {
        getUserOrders() {
            axios.get('/api/user/orders').then(res => {
                this.orders = res.data.orders
                console.log(this.orders)
            }).catch(err => {
                console.log(err)
            })
        },

        submitDelivery(orderId){
            axios.post(`/api/user/orders/submit`, { orderId: orderId })
                .then(() => {
                    this.getUserOrders()
                })
                .catch(error => {
                    console.error('Error succeded product:', error)
                })
        }
    }
}
</script>

<template>
    <template v-if="orders">
        <div class="container">
            <h1 class="text-center mt-5 mb-4">Current Orders</h1>
            <div class="row">
                <div class="col-md-6">
                    <div v-for="order in orders" class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Order #{{ order.id }}</h5>
                            <p class="card-text"><strong>User ID:</strong> {{ order.users_id }}</p>
                            <template v-if="order.courier_id">
                                <p class="card-text"><strong>Courier ID:</strong> {{ order.courier_id }}</p>
                                <p class="card-text"><strong>Courier name:</strong> {{ order.courierName }}</p>
                                <p class="card-text"><strong>Courier phone:</strong> {{ order.phoneCourier }}</p>
                            </template>
                            <p class="card-text"><strong>City:</strong> {{ order.city }}</p>
                            <p class="card-text"><strong>Store:</strong> {{ order.storeName }}</p>
                            <p class="card-text"><strong>Phone Number:</strong> {{ order.phoneNumber }}</p>
                            <p class="card-text"><strong>Address:</strong> {{ order.address }}</p>
                            <p class="card-text"><strong>Price:</strong> ${{ order.price }}</p>
                            <p class="card-text"><strong>Status:</strong> {{ order.status }}</p>
                            <p class="card-text"><strong>Created At:</strong> {{ order.created_at }}</p>
                            <template v-if="order.courier_id">
                                <p class="card-text"><strong>Updated At:</strong> {{ order.updated_at }}</p>
                            </template>
                            <template v-if="order.status == 1">
                                <div @click.prevent="submitDelivery(order.id)" class="btn btn-primary btn-sm">Succeed</div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</template>

<style>

</style>
