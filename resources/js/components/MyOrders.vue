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
                <div class="col-md-12">
                    <div v-for="order in orders" class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between">
                                <div class="w-50 text-center">
                                    <h5 class="card-title">Order #{{ order.id }}</h5>
                                    <template v-if="order.courier_id">
                                        <p class="card-text"><strong>Courier ID: </strong>{{ order.courier_id }}</p>
                                        <p class="card-text"><strong>Courier name:</strong> {{ order.courierName }}</p>
                                        <p class="card-text"><strong>Courier phone:</strong> {{ order.phoneCourier }}</p>
                                    </template>
                                    <p class="card-text"><strong>City:</strong> {{ order.city }}</p>
                                    <p class="card-text"><strong>Store:</strong> {{ order.storeName }}</p>
                                    <p class="card-text"><strong>Phone Number:</strong> {{ order.phoneNumber }}</p>
                                    <p class="card-text"><strong>Address:</strong> {{ order.address }}</p>
                                    <p class="card-text"><strong>Price:</strong> ${{ order.price }}</p>
                                    <p class="card-text"><strong>Status:</strong>
                                        <template v-if="order.status === 1"> order is accepted</template>
                                        <template v-else-if="order.status === 2"> delivered</template>
                                        <template v-else> awaiting delivery</template>
                                    </p>
                                    <p class="card-text"><strong>Created At:</strong> {{ order.created_at }}</p>
                                    <template v-if="order.courier_id">
                                        <p class="card-text"><strong>Updated At:</strong> {{ order.updated_at }}</p>
                                    </template>
                                </div>
                                <div class="w-50 text-center">
                                    <h5 class="card-title">Products</h5>

                                    <template v-if="order.products">
                                        <template v-for="(product, index) in order.products">
                                            <div class="card-text"><strong>Product {{index+1}}: </strong> {{ product.name}}.
                                                <strong>Price: </strong> {{ product.price}} <strong>Count: </strong> {{ product.count}}.</div>
                                            <template v-if="product.options">
                                                <p class="card-subtitle">Options:</p>
                                                <template v-for="options in product.options">
                                                    {{' ' + options.name}}
                                                </template>
                                            </template>
                                        </template>
                                    </template>

                                    <div class="mt-3 mb-3">
                                        <template v-if="order.status == 1">
                                            <div @click.prevent="submitDelivery(order.id)" class="btn btn-primary btn-sm">Succeed</div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</template>

<style>

</style>
