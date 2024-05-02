<script>
export default {
    name: "Courier panel",

    data() {
        return {
            availableOrders: null,
            currentOrders: null,
        }
    },

    mounted() {
        this.getCurrentOrders()
        if(!this.currentOrders) {
            this.getAvailableOrders()
        }
    },

    methods: {
        getAvailableOrders() {
            axios.get('/api/courier/availableOrders').then(res => {
                this.availableOrders = res.data.orders
                console.log(this.availableOrders)
            }).catch(err => {
                console.log(err)
            })
        },

        getCurrentOrders() {
            axios.get('/api/courier/currentOrder').then(res => {
                this.currentOrders = res.data.orders
                console.log(this.currentOrders)
            }).catch(err => {
                console.log(err)
            })
        },

        selectOrderForDelivery(orderId) {
            axios.post(`/api/courier/selectOrder`, { orderId: orderId })
                .then(() => {
                    this.availableOrders = null
                    this.getCurrentOrders()
                })
                .catch(error => {
                    console.error('Error adding product:', error)
                })
        }
    }
}

</script>

<template>

    <template v-if="currentOrders && currentOrders.length > 0">
        <div class="container">
            <h1 class="text-center mt-5 mb-4">Current Orders</h1>
            <div class="row">
                <div class="col-md-12">
                    <div v-for="order in currentOrders" class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between">
                                <div class="w-50 text-center">
                                    <h5 class="card-title">Order #{{ order.id }}</h5>
                                    <p class="card-text"><strong>User address:</strong> {{ order.address }}</p>
                                    <p class="card-text"><strong>User phone:</strong> {{ order.phoneNumber }}</p>
                                    <p class="card-text"><strong>City:</strong> {{ order.city }}</p>
                                    <p class="card-text"><strong>Store:</strong> {{ order.storeName }}</p>
                                    <p class="card-text"><strong>Address:</strong> {{ order.address }}</p>
                                    <p class="card-text"><strong>Price:</strong> ${{ order.price }}</p>
                                    <p class="card-text"><strong>Status:</strong> {{ order.status === '1' ? 'Pending' : 'Completed' }}</p>
                                    <p class="card-text"><strong>Created At:</strong> {{ order.created_at }}</p>
                                    <p class="card-text"><strong>Updated At:</strong> {{ order.updated_at }}</p>
                                </div>
                                <div class="w-50 text-center">
                                    <h5 class="card-title">Products</h5>
                                    <template v-if="order.products">
                                        <template v-for="(product, index) in order.products">
                                            <p class="card-text"><strong>Product {{index+1}}: </strong> {{ product.name}}. <strong>Price: </strong>. {{ product.price}} <strong>Count: </strong> {{ product.count}}.</p>
                                            <template v-if="product.options">
                                                <p class="card-subtitle">Options:</p>
                                                <template v-for="options in product.options">
                                                    {{' ' + options.name}}
                                                </template>
                                            </template>
                                        </template>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template v-if="availableOrders">

        <div class="container">
            <h1 class="text-center mt-5 mb-4">Available Orders</h1>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Order</th>
                            <th>User ID</th>
                            <th>City</th>
                            <th>Store</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="order in availableOrders">
                            <td>{{ order.id }}</td>
                            <td>{{ order.users_id }}</td>
                            <td>{{ order.city }}</td>
                            <td>{{ order.storeName }}</td>
                            <td>{{ order.phoneNumber }}</td>
                            <td>{{ order.address }}</td>
                            <td>${{ order.price }}</td>
                            <td>{{ order.status === '0' ? 'Pending' : 'Completed' }}</td>
                            <td>{{ order.created_at }}</td>
                            <td><button @click.prevent="selectOrderForDelivery(order.id)" class="btn btn-primary btn-sm">Process Order</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
</template>

<style scoped>

</style>
