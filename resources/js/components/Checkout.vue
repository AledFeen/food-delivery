<script>
export default {
    name: "Checkout",
    // props: ['basket'],

    mounted() {
        this.basket = JSON.parse(localStorage.getItem('basket'))
        this.checkBasket()
        console.log(this.basket)
    },

    data() {
        return {
            basket: null,
            phone: null,
            address: null
        }
    },

    methods: {
        checkBasket() {
            if (!Array.isArray(this.basket) && this.basket.length < 1) {
                this.$router.push({name: 'home.index'})
            }
        },

        checkout() {
            const city = localStorage.getItem('basketCity')
            const store = localStorage.getItem('basketStore')

            var phonePattern = /^\+?(\d{1,3})?\s?\(?(\d{3})\)?[-.\s]?(\d{3})[-.\s]?(\d{2})[-.\s]?(\d{2})$/;

            if(this.address && phonePattern.test(this.phone)) {
                axios.post('/api/checkout', {city: city, store: store, basket: this.basket, phone: this.phone, address: this.address})
                    .then( r => {
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
    <div class="text-light">Checkout</div>
        <div class="d-flex flex-column w-100">
            <div class="w-50 h-100 bg-light ps-5 pe-5">
                <div class="d-flex flex-column text-center">
                    <template v-if="basket && basket.length > 0" v-for="(item, index) in basket">
                        <div class="d-flex flex-column mt-1 mb-1">
                            <h5>Продукт {{ index + 1 }}</h5>
                            <div>Назва: {{ item.product.name }}</div>
                            <div>Обрані опції:</div>
                            <template v-for="opt in item.options">
                                <template v-if="opt.obj">
                                    <div> {{ opt.category + ':' + opt.obj.name }}</div>
                                </template>
                                <template v-if="opt.objs">
                                    <div> {{ opt.category + ': ' }}
                                        <template v-for="o in opt.objs">
                                            {{ o.name }}
                                        </template>
                                    </div>
                                </template>
                            </template>
                            <div>Кількість: {{ item.count }}</div>
                            <div>Ціна: {{ item.price }}</div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="w-50 ps-5 pe-5">
                <input v-model="phone" type="text" placeholder="phone" class="form-control mt-3 mb-3">
                <input v-model="address" type="text" placeholder="address" class="form-control mt-3 mb-3">
                <div @click.prevent="checkout" class="btn btn-primary w-100">Замовити</div>
            </div>
        </div>
</template>

<style scoped>

</style>
