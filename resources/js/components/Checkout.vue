<script>
export default {
    name: "Checkout",
    // props: ['basket'],

    mounted() {
        this.basket = JSON.parse(localStorage.getItem('basket'))
        this.checkBasket()
        this.deliveryPrice = localStorage.getItem('deliveryPrice')
        this.countTotalPrice()
    },

    data() {
        return {
            basket: null,
            phone: null,
            address: null,
            deliveryPrice: 0,
            totalPrice: 0
        }
    },

    methods: {
        checkBasket() {
            if (!Array.isArray(this.basket) && this.basket.length < 1) {
                this.$router.push({name: 'home.index'})
            }
        },

        countTotalPrice() {
            let total = this.basket.reduce((accumulator, currentValue) => {
                return accumulator + currentValue.price;
            }, 0);

            this.totalPrice = parseInt(total) + parseInt(this.deliveryPrice)
        },

        checkout() {
            const city = localStorage.getItem('basketCity')
            const store = localStorage.getItem('basketStore')

            var phonePattern = /^\+?(\d{1,3})?\s?\(?(\d{3})\)?[-.\s]?(\d{3})[-.\s]?(\d{2})[-.\s]?(\d{2})$/;

            if (this.address && phonePattern.test(this.phone)) {
                axios.post('/api/checkout', {
                    city: city,
                    store: store,
                    basket: this.basket,
                    phone: this.phone,
                    address: this.address
                })
                    .then(r => {
                        console.log(r)
                    })
                    .catch(error => {
                        console.error('Error fetching store:', error)
                        window.alert("Error. Try again.");
                    })
            } else {
                window.alert('Uncorrected validation')
            }

        }
    }
}
</script>

<template>
    <div class="d-flex justify-content-center align-items-center vh-50">
        <div class="text-light">Checkout</div>
        <div class="d-flex flex-column w-50">
            <div class="w-100 h-100 bg-light ps-5 pe-5">
                <div class="d-flex flex-column text-center">
                    <template v-if="basket && basket.length > 0" v-for="(item, index) in basket">
                        <div class="d-block border border-1 m-3 rounded-3">
                            <h5 class="text-center mt-3">Item {{ index + 1 }}</h5>
                            <div class="d-flex flex-row mt-1 mb-1 justify-content-around">
                                <div>{{ item.count }}x</div>
                                <div> {{ item.product.name }}</div>
                                <div>{{ item.price }} grn</div>
                            </div>
                            <div v-if="item.options.length > 0" class="fw-semibold">Selected options:</div>
                            <div class="d-flex flex-row flex-wrap justify-content-around">
                                <template v-for="opt in item.options">
                                    <template v-if="opt.obj">
                                        <div> {{ opt.category + ':' + ' ' + opt.obj.name + ' '}}</div>
                                    </template>
                                    <template v-if="opt.objs">
                                        <div> {{ opt.category + ': ' }}
                                            <template v-for="o in opt.objs">
                                                {{' ' + o.name + ' ' }}
                                            </template>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="w-100 ps-5 pe-5">
                <input v-model="phone" type="text" placeholder="phone" class="form-control mt-3 mb-3">
                <input v-model="address" type="text" placeholder="address" class="form-control mt-3 mb-3">
                <div>Delivery price: {{deliveryPrice}}</div>
                <div class="fw-bold">For total: {{totalPrice}}</div>
                <div @click.prevent="checkout" class="btn btn-primary w-100 mt-3 mb-3">Замовити</div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
