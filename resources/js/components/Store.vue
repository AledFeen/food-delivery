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
    },

    data() {
        return {
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
            selectedOptions: []
        }
    },

    watch: {
        productCategories(newValue, oldValue) {
            this.makeOptions()
        }
    },

    methods: {
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
            axios.get('/api/store/product/categories', {
                params: {productId: this.selectedProduct.id}
            })
                .then(res => {
                    console.log(res.data)
                    this.productCategories = res.data.productCategories
                    this.storeProducts = res.data.storeProducts
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
                if(category.name === el.category) {
                    if(category.type === 'Single') {
                        if (el.obj === null) {
                            const element1 = document.getElementById('option_' + category.name + '_' + product.name)
                            element1.classList.remove("text-light")
                            el.obj = product
                        } else if (el.obj === product) {
                            const element = document.getElementById('option_' + el.category + '_' + el.obj.name)
                            element.classList.add("text-light")
                            el.obj = null
                        } else {
                            const element = document.getElementById('option_' + el.category + '_' + el.obj.name)
                            element.classList.add("text-light")
                            const element1 = document.getElementById('option_' + category.name + '_' + product.name)
                            element1.classList.remove("text-light")
                            el.obj = product
                        }
                    } else {

                    }
                }
            })
        }
    },
}
</script>

<template>
    <main class="d-flex flex-row mt-3">
        <div class="w-25">
            <div class="d-flex flex-column align-self-center">
                <template v-for="category in categories" :key="category.id">
                    <template v-if="category.childs">
                        <div class="sidebar-item p-2 w-100 d-flex flex-row justify-content-between">
                            <div class="w-75" @click.prevent="clickItem(category)">
                                <div class="text-light">{{ category.name }}</div>
                            </div>
                            <div class="btn btn-light" @click.prevent="toggleChildVisibility(category.id)"></div>
                        </div>
                        <div :id="'childs_' + category.id" class="d-none">
                            <template v-for="child in category.childs" :key="child.id">
                                <div class="sidebar-sub-item p-2 w-100 d-flex flex-row justify-content-between"
                                     @click.prevent="clickItem(child)">
                                    <div class="text-light">{{ child.name }}</div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <div class="sidebar-item p-2 w-100 d-flex flex-row justify-content-between"
                             @click.prevent="clickItem(category)">
                            <div class="text-light">{{ category.name }}</div>
                        </div>
                    </template>
                </template>
            </div>
        </div>

        <div v-if="selectedCategory" class="w-50 ms-3">
            <a id="openModalProduct" href="#" class="d-none" data-bs-target="#ModalToggleProduct"
               data-bs-toggle="modal">Add
                product</a>
            <div class="d-flex flex-column mt-3">
                <template v-if="!selectedCategory.childs && products">
                    <div class="d-flex flex-row flex-wrap">
                        <template v-for="product in products">
                            <div class="d-block w-25 bg-light ms-3 me-3 mb-3"
                                 @click.prevent="opedProductModal(product)">
                                <div>{{ product.name }}</div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
        <div class="w-25">
            <div class="h-100 bg-light">
                <template v-if="basket.length > 0" v-for="item in basket">

                </template>
            </div>
        </div>
    </main>

    <div class="modal fade" id="ModalToggleProduct" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Add product</h1>
                    <button id="btnCloseProduct" type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-row">
                        <template v-if="productCategories && productCategories.length > 0">
                            <div class="w-50 me-3">
                                <div class="d-flex flex-column">
                                    <template v-for="category in productCategories">
                                        <div class="sidebar-item mb-1 text-light d-flex flex-column">
                                            <div>{{ category.name }}</div>
                                            <template v-if="category.type === 'Single'">
                                                <div>Оберіть один товар з категорії</div>
                                            </template>
                                            <template v-else-if="category.type === 'Multi'">
                                                <div>Ви можете обрати декілька додаткових позицій</div>
                                                <div>Максимум: {{ category.count }}</div>
                                            </template>
                                        </div>
                                        <template v-for="product in category.items">
                                            <div class="sidebar-sub-item mb-1 text-light"
                                                :id="'option_' + category.name + '_' + product.name" @click.prevent="selectOption(category, product)"> {{ product.name }}
                                            </div>
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
                            </div>
                        </div>
                        <div v-else class="w-100">
                            <div v-if="selectedProduct" class="d-flex flex-column">
                                <img :src="'/storage/images/products/' + selectedProduct.imagePath" alt="Image"
                                     class="mw-100 mh-100">
                                <div>{{ selectedProduct.name }}</div>
                                <div>Ціна: {{ selectedProduct.price }}</div>

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
    background-color: lightgray;
}

.sidebar-sub-item {
    background-color: lightblue;
}

.sidebar-item:hover {
    background-color: #e2e2e2; /* Изменение фона при наведении */
    cursor: pointer; /* Изменение типа курсора */
}

.sidebar-sub-item:hover {
    background-color: #e2e2e2; /* Изменение фона при наведении */
    cursor: pointer; /* Изменение типа курсора */
}
</style>
