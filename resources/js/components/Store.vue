<script>
export default {
    name: "Store",
    props: ['storeId'],

    mounted() {
        this.getStore()
        this.getCategories()
        this.setDefaultSelectedCategory()
    },

    updated() {
        this.setDefaultSelectedCategory()
        this.checkUser();
    },

    data() {
        return {
            token: null,
            store: null,
            categories: [],
            maxPath: 0,
            name: null,
            basket: [],

            products: [],
            storeProducts: [],
            productCategories: [],


            selectedCategory: null,
            selectedProduct: null,
            selectedOptions: [],
            selectedCount: 1,
            totalProductPrice: 0,

            email: null,
            password: null
        }
    },

    watch: {
        productCategories(newValue, oldValue) {
            this.makeOptions()
        },
        selectedCount(newValue, oldValue) {
            this.setTotalPrice()
        }
    },

    methods: {

        checkUser() {
            this.token = localStorage.getItem('x-xsrf-token')
        },

        getStore() {
            axios.get('/api/store', {
                params: {id: this.storeId}
            }).then(res => {
                this.store = res.data.store
            }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        getCategories() {
            axios.get('/api/store/categories', {
                params: {store_id: this.storeId}
            }).then(res => {
                this.categories = res.data.categories
            }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        clickItem(category) {
            this.selectedCategory = category
            this.getProductsForCategory(category.id)
        },

        toggleChildVisibility(id) {
            const btn = document.getElementById('btn_category' + id);
            if (btn.alt === "btn_open") {
                btn.alt = 'btn_close'
                btn.style.transform = 'rotate(-90deg)';
            } else {
                btn.alt = 'btn_open'
                btn.style.transform = 'rotate(0deg)';
            }

            const childsBlock = document.getElementById('childs_' + id);
            if (childsBlock.classList.contains('d-none')) {
                childsBlock.classList.remove('d-none');
                childsBlock.classList.add('d-block');
            } else {
                childsBlock.classList.remove('d-block');
                childsBlock.classList.add('d-none');
            }
        },

        setDefaultSelectedCategory() {
            if (!this.selectedCategory && this.categories) {
                this.selectedCategory = this.categories[0]
            }
        },

        getProductsForCategory(categoryId) {
            axios.get('/api/store/category/products', {
                params: {categoryId: categoryId}
            }).then(res => {
                this.products = res.data.products
            }).catch(error => {
                console.error('Error fetching store:', error)
                window.alert("Error. Try again.");
            })
        },

        getProductCategories() {
            axios.get('/api/home/store/product/categories', {
                params: {productId: this.selectedProduct.id, storeId: this.storeId}
            })
                .then(res => {
                    this.productCategories = res.data.productCategories
                    this.storeProducts = res.data.storeProducts
                    this.selectedCount = 1
                    this.totalProductPrice = this.selectedProduct.price
                }).catch(error => {
                console.error('Error fetching store:', error)
                window.alert("Error. Try again.");
            })
        },

        makeOptions() {
            let options = []
            this.productCategories.forEach(element => {
                if (element.type === 'Single') {
                    let option = {
                        category: element.name,
                        obj: null
                    }
                    options.push(option)
                } else if (element.type === 'Multi') {
                    let option = {
                        category: element.name,
                        objs: []
                    };
                    for (let j = 0; j < element.count; j++) {
                        option.objs.push(null);
                    }
                    options.push(option);
                }
            })
            this.selectedOptions = options
        },

        opedProductModal(product) {
            this.selectedProduct = product
            this.getProductCategories()

            const modalOpenButton = document.getElementById('openModalProduct');
            modalOpenButton.click();
        },

        selectOption(category, product) {
            this.selectedOptions.forEach(el => {
                if (category.name === el.category) {
                    if (category.type === 'Single') {
                        if (el.obj === null) {
                            const element1 = document.getElementById('option_' + category.name + '_' + product.name)
                            element1.classList.add("bg-primary")
                            el.obj = product
                        } else if (el.obj === product) {
                            const element = document.getElementById('option_' + el.category + '_' + el.obj.name)
                            element.classList.remove("bg-primary")
                            el.obj = null
                        }
                         else {
                            const element = document.getElementById('option_' + el.category + '_' + el.obj.name)
                            element.classList.remove("bg-primary")
                            const element1 = document.getElementById('option_' + category.name + '_' + product.name)
                            element1.classList.add("bg-primary")
                            el.obj = product
                        }
                    } else {
                        let isNext = true;
                        for (let i = 0; i < el.objs.length; i++) {
                            const item = el.objs[i];
                            if (isNext === true) {
                                if (item === null) {
                                    const element = document.getElementById('option_' + category.name + '_' + product.name)
                                    element.classList.add("bg-primary")
                                    el.objs[i] = product;
                                    isNext = false;
                                } else if (item.name === product.name) {
                                    const element = document.getElementById('option_' + el.category + '_' + item.name)
                                    element.classList.remove("bg-primary")
                                    el.objs[i] = null;
                                    isNext = false;
                                }
                            }
                        }
                    }
                }
            })
        },

        clearClass() {
            try {
                if (this.selectedOptions.length > 0) {
                    this.selectedOptions.forEach(el => {
                        if (el.obj) {
                            const element = document.getElementById('option_' + el.category + '_' + el.obj.name)
                            element.classList.add("text-light")
                        } else if (el.objs) {
                            el.objs.forEach(item => {
                                if (el != null) {
                                    const element = document.getElementById('option_' + el.category + '_' + item.name)
                                    element.classList.add("text-light")
                                }
                            })
                        }
                    })
                }
            } catch (e) {
            }
        },

        plusCount() {
            this.selectedCount++
        },

        minusCount() {
            if (this.selectedCount > 1) {
                this.selectedCount--
            }
        },

        setTotalPrice() {
            this.totalProductPrice = this.selectedProduct.price * this.selectedCount
        },

        addItemToBasket() {
            let isTrue = true

            if (this.selectedOptions.length > 0) {
                this.selectedOptions.forEach(el => {
                    if (el.obj === null) {
                        isTrue = false
                    } else if (el.objs) {
                        el.objs.forEach(item => {
                            if (item === null) {
                                isTrue = false
                            }
                        })
                    }
                })

                if (isTrue) {
                    let item = {
                        product: this.selectedProduct,
                        options: this.selectedOptions,
                        count: this.selectedCount,
                        price: this.totalProductPrice
                    }
                    this.basket.push(item)
                } else {
                    window.alert("Ви не обрали потрібні опції");
                }
            } else {
                let item = {
                    product: this.selectedProduct,
                    options: this.selectedOptions,
                    count: this.selectedCount,
                    price: this.totalProductPrice
                }
                this.basket.push(item)
            }
        },

        deleteItemFromBasket(item) {
            let indexToRemove = this.basket.findIndex(element => {
                return element === item
            });

            if (indexToRemove !== -1) {
                this.basket.splice(indexToRemove, 1);
            }
        },

        clickCheckout() {
            if (localStorage.getItem('x-xsrf-token')) {
                localStorage.setItem('basket', JSON.stringify(this.basket))
                localStorage.setItem('basketStore', this.storeId)
                this.$router.push({name: 'checkout'})
            } else {
                this.$router.push({name: 'user.login'})
            }
        }


    }
}
</script>

<template v-if="store">
    <main class="d-flex flex-row mt-3">

        <!-- Categories -->
        <div class="w-25">
            <div class="d-flex flex-column align-self-center">
                <template v-for="category in categories" :key="category.id">
                    <template v-if="category.childs">
                        <div class="sidebar-item-no-hover p-2 w-100 d-flex flex-row justify-content-between">
                            <div class="w-75">
                                <div class="text-dark">{{ category.name }}</div>
                            </div>
                            <img :src="'/storage/images/static/arrow_left.png'" width="12px" height="12px" class="mt-1"
                                 :id="'btn_category' + category.id" @click.prevent="toggleChildVisibility(category.id)"
                                 alt="btn_open">
                        </div>
                        <div class="border border-1 border-bottom w-100"></div>
                        <div :id="'childs_' + category.id" class="d-none">
                            <template v-for="child in category.childs" :key="child.id">
                                <div class="sidebar-sub-item p-2 w-100 d-flex flex-row justify-content-between"
                                     @click.prevent="clickItem(child)">
                                    <div class="text-dark">{{ child.name }}</div>
                                </div>
                                <div class="border border-1 border-bottom w-100"></div>
                            </template>

                        </div>
                    </template>
                    <template v-else>
                        <div class="sidebar-item p-2 w-100 d-flex flex-row justify-content-between"
                             @click.prevent="clickItem(category)">
                            <div class="text-dark">{{ category.name }}</div>
                        </div>
                        <div class="border border-1 border-bottom w-100"></div>
                    </template>
                </template>
            </div>
        </div>

        <!-- Center -->
        <div class="w-50 ms-3">
            <!-- Store -->
            <div v-if="store" class="d-flex flex-row border border-1 rounded-3">
                <div class="d-flex flex-column justify-content-center w-25 ms-1">
                    <img :src="'/storage/images/stores/' + store.image" width="100%" class="rounded-3 img p-1"
                         alt="Image">
                </div>
                <div class="w-75 d-flex flex-column ms-3 p-1">
                    <h4>{{ store.name }}</h4>
                    <div class="form-text">{{ store.description }}</div>
                    <div class="d-flex flex-row">
                        <div>{{ store.type_store }}</div>
                        <div class=" ms-3">{{ store.category }}</div>
                    </div>
                </div>
            </div>
            <!-- Products -->
            <template v-if="selectedCategory" class="ms-3">
                <a id="openModalProduct" href="#" class="d-none" data-bs-target="#ModalToggleProduct"
                   data-bs-toggle="modal">Add product</a>
                <h3 class="text-center mt-3">{{ selectedCategory.name }}</h3>
                <div class="d-flex flex-column mt-3">
                    <template v-if="!selectedCategory.childs && products">
                        <div class="d-flex flex-row flex-wrap">
                            <template v-for="product in products">
                                <div class="d-flex flex-column col-md-4 ps-3 pe-3 mb-3 pt-3 rounded-3 point"
                                     @click.prevent="opedProductModal(product)">

                                    <div class="h-75 d-block">
                                        <img :src="'/storage/images/products/' + product.imagePath" width="100%"
                                             height="100%" class="img rounded-3 card-img-top" alt="Image">
                                    </div>
                                    <div class="card-body">
                                        <div class="mt-2 card-title">{{ product.name }}</div>
                                        <div class="fw-bold card-subtitle">{{ product.price }}</div>
                                        <div class="card-subtitle">delivery price</div>
                                    </div>
                                    <div class="overlay"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>


        <!-- Basket -->
        <div class="w-25 mb-3">
            <div class="bg-light border border-1 ms-3 me-3 rounded-3">
                <div class="d-flex flex-column text-center">
                    <h4 class="mt-3">Basket</h4>
                    <template v-if="basket.length > 0" v-for="(item, index) in basket">
                        <div class="d-block border border-1 m-3 rounded-3">
                            <h5 class="text-center mt-3">Item {{index + 1}}</h5>
                            <div class="d-flex flex-row  mt-1 mb-1 justify-content-around">
                                <div class="w-25">{{ item.count }}x</div>
                                <div class="d-flex flex-row flex-wrap w-25">
                                    <div> {{ item.product.name }}</div>
                                </div>
                                <div class="w-25">{{ item.price }} grn</div>
                            </div>
                            <div @click.prevent="deleteItemFromBasket(item)" class="btn btn-danger ms-5 me-5 mb-3 mt-3">Видалити</div>
                        </div>
                    </template>

                    <template v-if="basket.length > 0 && token">
                        <div @click.prevent="clickCheckout" class="btn btn-primary ms-5 me-5 mt-3 mb-3">Оформити</div>
                    </template>
                    <template v-if="!token">
                        <div class="text-danger">You need authorize to checkout</div>
                    </template>
                </div>
            </div>
        </div>
    </main>


    <!-- Modal select product -->

    <div class="modal fade" id="ModalToggleProduct" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Add product</h1>
                    <button @click.prevent="clearClass" id="btnCloseProduct" type="button" class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-row">
                        <template v-if="productCategories && productCategories.length > 0">
                            <div class="w-50 me-3">
                                <div class="d-flex flex-column">
                                    <template v-for="category in productCategories">
                                        <div class="mb-1 text-dark d-flex flex-column">
                                            <h5 class="text-center mt-2">{{ category.name }}</h5>
                                            <template v-if="category.type === 'Single'">
                                                <div>Select only one</div>
                                            </template>
                                            <template v-else-if="category.type === 'Multi'">
                                                <div>Select some products. Count: {{ category.count }}</div>

                                            </template>
                                        </div>
                                        <template v-for="product in category.items">
                                            <div class="sidebar-sub-item mb-1 text-dark"
                                                 :id="'option_' + category.name + '_' + product.name"
                                                 @click.prevent="selectOption(category, product)"> {{ product.name }}
                                            </div>
                                            <div class="border border-1 border-bottom w-100"></div>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <div v-if="productCategories && productCategories.length > 0" class="w-50">
                            <div v-if="selectedProduct" class="d-flex flex-column">
                                <img :src="'/storage/images/products/' + selectedProduct.imagePath" alt="Image"
                                     class="mw-100 mh-100">
                                <div>{{ selectedProduct.name }}</div>
                                <div>Ціна: {{ selectedProduct.price }}</div>
                                <div class="d-flex flex-row justify-content-center">
                                    <div class="btn btn-dark" @click.prevent="minusCount">-</div>
                                    <div class="ms-3 me-3 pt-2">{{ selectedCount }}</div>
                                    <div class="btn btn-dark" @click.prevent="plusCount">+</div>
                                </div>
                                <div class="text-center mt-1">Загалом: {{ this.totalProductPrice }}</div>
                                <div class="btn btn-dark text-center mt-1" @click.prevent="addItemToBasket">Додати в
                                    кошик
                                </div>
                            </div>
                        </div>
                        <div v-else class="w-100">
                            <div v-if="selectedProduct" class="d-flex flex-column">
                                <div class="d-flex flex-row justify-content-center">
                                    <img :src="'/storage/images/products/' + selectedProduct.imagePath" alt="Image"
                                         width="50%" height="50%">

                                </div>
                                <div>{{ selectedProduct.name }}</div>
                                <div>Ціна: {{ selectedProduct.price }}</div>
                                <div class="d-flex flex-row justify-content-center">
                                    <div class="btn btn-dark" @click.prevent="minusCount">-</div>
                                    <div class="ms-3 me-3 pt-2">{{ selectedCount }}</div>
                                    <div class="btn btn-dark" @click.prevent="plusCount">+</div>
                                </div>
                                <div class="text-center mt-1">Загалом: {{ this.totalProductPrice }}</div>
                                <div class="btn btn-dark text-center mt-1" @click.prevent="addItemToBasket">Add to basket
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>


</template>

<style scoped>
.sidebar-item {
    background-color: rgba(0, 0, 0, 0); /* Прозрачный цвет слоя */
    transition: background-color 0.3s ease;
}

.sidebar-item-no-hover {

}

.sidebar-sub-item {
    background-color: rgba(0, 0, 0, 0); /* Прозрачный цвет слоя */
    transition: background-color 0.3s ease;
}

.sidebar-item:hover {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer; /* Изменение типа курсора */
}

.sidebar-sub-item:hover {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer; /* Изменение типа курсора */
}

.point {
    position: relative;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0); /* Прозрачный цвет слоя */
    transition: background-color 0.3s ease; /* Плавный переход цвета фона */
    border-radius: 6px;
}

.overlay:hover {
    background-color: rgba(0, 0, 0, 0.5); /* Затемненный цвет слоя при наведении */
}

.img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    object-position: center;
}
</style>
