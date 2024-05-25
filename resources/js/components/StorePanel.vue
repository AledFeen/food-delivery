<script>
export default {
    name: "Categories",

    data() {
        return {
            categories: null,
            maxPath: 0,
            name: null,
            isOpen: [],

            products: null,
            storeProducts: null,
            productCategories: null,

            isMainAdded: null,
            selectedCategory: null,
            selectedProduct: null,

            image: null,
            productName: null,
            productDescription: null,
            productPrice: null,

            whatProductSelected: null,
            productCategoryName: null,
            selectedProductItem: null,
            selectedProductCategory: null
        }
    },

    mounted() {
        this.getCategories()
        //this.setDefaultSelectedCategory()
    },

    watch: {
        $categories(newValue, oldValue) {
            this.setDefaultSelectedCategory()
        }
    },

    methods: {
        getCategories() {
            axios.get('/api/user/store/categories')
                .then(res => {
                    console.log(res.data)
                    this.categories = res.data.categories
                    this.maxPath = res.data.maxPath
                }).catch(error => {
                console.error('Error fetching store:', error)
                window.alert("Error. Try again.");
            })
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

        selectCategoryForAddCategory() {
            this.isMainAdded = false
            const modalOpenButton = document.getElementById('openModalCategory');
            modalOpenButton.click();
        },

        selectCategoryForAddProduct() {
            const modalOpenButton = document.getElementById('openModalProduct');
            modalOpenButton.click();
        },

        openAddMainCategory() {
            this.isMainAdded = true
            const modalOpenButton = document.getElementById('openModalCategory');
            modalOpenButton.click();
        },

        addMainCategory() {
            console.log('main')
            const formData = new FormData()
            formData.append('name', this.name)
            axios.post('/api/store/add/mainCategory', formData).then(() => {
                this.getCategories()
                const closeBtn = document.getElementById('btnCloseCategory');
                closeBtn.click();
            }).catch(error => {
                console.error('Error fetching store:', error)
                window.alert("Error. Try again. Description:" + error);
            })
        },

        addSubCategory() {
            const formData = new FormData()
            formData.append('name', this.name)
            formData.append('parent', this.selectedCategory.id)

            axios.post('/api/store/add/subCategory', formData).then(() => {
                this.getCategories()
                const closeBtn = document.getElementById('btnCloseCategory');
                closeBtn.click();
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

        handleFileChange() {
            if (this.$refs.fileInput) {
                this.image = this.$refs.fileInput.files[0]
            }
        },

        addProduct() {
            const formData = new FormData()
            formData.append('name', this.productName)
            formData.append('description', this.productDescription)
            formData.append('image', this.image)
            formData.append('price', this.productPrice)
            formData.append('category_id', this.selectedCategory.id)
            axios.post('/api/store/add/product', formData)
                .then(() => {
                    this.getProductsForCategory(this.selectedCategory.id)
                    //this.selectedCategory = this.selectedCategory
                    const closeBtn = document.getElementById('btnCloseProduct');
                    closeBtn.click();
                })
                .catch(error => {
                    console.error('Error fetching store:', error)
                    window.alert("Error. Try again. " + error.response.data.message);
                })
        },

        updateProduct() {
            const formData = new FormData()
            formData.append('product_id', this.selectedProduct.id)
            formData.append('name', this.selectedProduct.name)
            formData.append('description', this.selectedProduct.description)
            formData.append('prevImage', this.selectedProduct.imagePath)
            formData.append('image', this.image)
            formData.append('price', this.selectedProduct.price)
            formData.append('category_id', this.selectedCategory.id)

            axios.post('/api/store/update/product', formData)
                .then(() => {
                    this.getCategories()
                })
                .catch(error => {
                    console.error('Error fetching store:', error)
                    window.alert("Error. Try again. " + error.response.data.message);
                })
        },

        opedEditProductModal(product) {
            this.selectedProduct = product
            this.getProductCategories()
            const modalOpenButton = document.getElementById('openModalEditProduct');
            modalOpenButton.click();
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

        showProductAddCategory() {
            this.whatProductSelected = 'addCategory'
        },

        selectProductCategory(category) {
            this.whatProductSelected = 'category'
            this.selectedProductCategory = category
        },

        selectProductCategoryItem(product, category) {
            this.whatProductSelected = 'item'
            this.selectedProductItem = product
            this.selectedProductCategory = category
        },

        addProductCategory() {

            const selectElement = document.getElementById("typeSelect")
            const selectedOption = selectElement.options[selectElement.selectedIndex].value
            const name = document.getElementById("productCategoryName")
            const count = document.getElementById("countEl")
            const formData = new FormData()

            if (name.value) {
                formData.append('type', selectedOption)
                formData.append('name', name.value)
                formData.append('count', count.value)
                formData.append('productId', this.selectedProduct.id)
                axios.post('/api/store/product/add/category', formData).then(() => {
                    this.getProductCategories()
                }).catch(error => {
                    console.error('Error fetching store:', error)
                    window.alert("Error. Try again. Description:" + error);
                })
            }
        },

        addItemToProductCategory() {
            const formData = new FormData()
            const selectElement = document.getElementById("productSelect")
            const selectedOption = selectElement.options[selectElement.selectedIndex].value
            formData.append('productId', selectedOption)
            formData.append('categoryId', this.selectedProductCategory.id)
            formData.append('parentId', this.selectedProduct.id)
            axios.post('/api/store/product/category/add/item', formData).then(() => {
                this.getProductCategories()
            }).catch(error => {
                console.error('Error fetching store:', error)
                window.alert("Error. Try again. Description:" + error);
            })
        },

        deleteItemFromProductCategory() {
            const data = {
                category: this.selectedProductCategory.id,
                item: this.selectedProductItem.id,
                parent: this.selectedProduct.id
            }
            axios.delete(`/api/store/product/category/delete/item`, {data})
                .then(() => {
                    this.whatProductSelected = 'category'
                    this.selectedProductCategory = this.selectedProductCategory
                    this.getProductCategories()
                })
                .catch(error => {
                    console.error('Error deleting product:', error)
                })
        },

        deleteCategoryFromProduct() {
            const data = {
                category: this.selectedProductCategory.id,
                parent: this.selectedProduct.id
            }
            axios.delete(`/api/store/product/delete/category`, {data})
                .then(() => {
                    this.whatProductSelected = 'addCategory'
                    this.selectedProductCategory = null
                    this.getProductCategories()
                })
                .catch(error => {
                    console.error('Error deleting product:', error)
                })
        },

        deleteProduct() {
            axios.delete(`/api/store/delete/product`, { data: { product: this.selectedProduct.id } })
                .then(() => {

                    const btnCloseEditProduct = document.getElementById('btnCloseEditProduct');
                    if (btnCloseEditProduct) {
                        btnCloseEditProduct.click();
                    }
                    this.getProductsForCategory(this.selectedCategory.id)
                })
                .catch(error => {
                    console.error('Error deleting product:', error)
                })
        },

        deleteSubcategory() {
            axios.delete(`/api/store/delete/subCategory`, { data: { category: this.selectedCategory.id } })
                .then(() => {
                   this.selectedCategory = null
                   this.getCategories()
                   this.getProductsForCategory(this.selectedCategory.id)
                })
                .catch(error => {
                    console.error('Error deleting product:', error)
                })
        },

        deleteCategory() {
            axios.delete(`/api/store/delete/category`, { data: { category: this.selectedCategory.id } })
                .then(() => {
                    this.selectedCategory = null
                    this.getCategories()
                    this.getProductsForCategory(this.selectedCategory.id)
                })
                .catch(error => {
                    console.error('Error deleting product:', error)
                })
        }
    }
}
</script>

<template>
    <main class="d-flex flex-row">
        <a id="openModalCategory" href="#" class="d-none" data-bs-target="#ModalToggleCategory" data-bs-toggle="modal">Add
            root category</a>
        <a id="openModalProduct" href="#" class="d-none" data-bs-target="#ModalToggleProduct" data-bs-toggle="modal">Add
            product</a>
        <a id="openModalEditProduct" href="#" class="d-none" data-bs-target="#ModalToggleEditProduct"
           data-bs-toggle="modal">Add
            product</a>

        <div class="w-25">
            <div class="d-flex flex-column align-self-center">
                <a href="#" @click.prevent="openAddMainCategory" class="btn btn-secondary w-100 mt-3 mb-2">Add
                    category</a>
                <template v-for="category in categories" :key="category.id">
                    <template v-if="category.childs">
                        <div class="sidebar-item p-2 w-100 d-flex flex-row justify-content-between">
                            <div class="w-75" @click.prevent="clickItem(category)">
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

        <div v-if="selectedCategory && products" class="w-75 ms-3">
            <div class="d-flex flex-column mt-3">
                <h3 class="text-center mt-3">{{ selectedCategory.name }}</h3>
                <a v-if="(selectedCategory.path === '/' && products.length === 0)" href="#"
                   @click.prevent="selectCategoryForAddCategory(selectedCategory.id)"
                   class="btn btn-secondary w-100 mt-2 mb-2">Add category</a>
                <a v-if="(selectedCategory.childs) || (selectedCategory.path === '/' && !products)" href="#"
                   @click.prevent="deleteCategory"
                   class="btn btn-danger w-100 mt-2 mb-2">Delete category</a>
                <a v-if="selectedCategory && selectedCategory.forProducts" href="#"
                   @click.prevent="selectCategoryForAddProduct(selectedCategory.id)"
                   class="btn mt-2 mb-2 btn-secondary w-100">Add
                    product</a>
                <a v-if="selectedCategory && !selectedCategory.childs" href="#"
                   @click.prevent="deleteSubcategory"
                   class="btn mt-2 mb-2 btn-danger w-100">delete subcategory</a>

                <template v-if="selectedCategory" class="ms-3">
                    <a id="openModalProduct" href="#" class="d-none" data-bs-target="#ModalToggleProduct"
                       data-bs-toggle="modal">Add product</a>

                    <div class="d-flex flex-column mt-3">
                        <template v-if="!selectedCategory.childs && products">
                            <div class="d-flex flex-row flex-wrap mb-5">
                                <template v-for="product in products">
                                    <div class="d-flex flex-column col-md-2 ps-3 pe-3 mb-3 pt-3 rounded-3 point"
                                         style="max-height: 400px"
                                         @click.prevent="opedEditProductModal(product)">
                                        <div class="h-75 d-block">
                                            <img :src="'/storage/images/products/' + product.imagePath" width="100%"
                                                 height="100%" class="img rounded-3 card-img-top" alt="Image">
                                        </div>
                                        <div class="card-body">
                                            <div class="mt-2 card-title">{{ product.name }}</div>
                                            <div class="fw-bold card-subtitle">{{ product.price }}</div>
                                        </div>
                                        <div class="overlay"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

            </div>
        </div>


        <!-- Modal 1 -->

        <div class="modal fade" id="ModalToggleCategory" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
             tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Add category</h1>
                        <button id="btnCloseCategory" type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <main class="align-self-center">
                            <div class="form-floating mb-3 mt-3">
                                <input type="text" class="form-control" id="name" placeholder="" v-model="name">
                                <label for="name">Name</label>
                            </div>
                        </main>

                    </div>
                    <div class="modal-footer">

                        <template v-if="!isMainAdded">
                            <button href="#" class="btn btn-primary w-100" @click.prevent="addSubCategory">
                                Add subcategory
                            </button>
                        </template>
                        <template v-else>
                            <button class="btn btn-primary w-100" @click.prevent="addMainCategory">
                                Add category
                            </button>
                        </template>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 2 -->

        <div class="modal fade" id="ModalToggleProduct" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
             tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Add product</h1>
                        <button id="btnCloseProduct" type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="name" placeholder="" v-model="productName">
                            <label for="name">Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <div class="input-group">
                                <span class="input-group-text">Description</span>
                                <textarea v-model="productDescription" class="form-control"
                                          aria-label="With textarea"></textarea>
                            </div>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="number" class="form-control" id="price" placeholder="" v-model="productPrice">
                            <label for="price">Price</label>
                        </div>

                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="inputGroupFile" ref="fileInput"
                                   @change="handleFileChange">
                            <label class="input-group-text" for="inputGroupFile">Upload</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click.prevent="addProduct">Add product</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 3 -->

        <div class="modal fade" id="ModalToggleEditProduct" aria-hidden="true"
             aria-labelledby="exampleModalToggleLabel3"
             tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Product</h1>
                        <button id="btnCloseEditProduct" type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-row" v-if="selectedProduct">
                            <div class="w-50 me-3">
                                <img :src="'/storage/images/products/' + selectedProduct.imagePath" alt="Image"
                                     class="mw-100 mh-100">
                            </div>
                            <div class="w-50">
                                <div class="form-floating mb-3 mt-3">
                                    <input type="text" class="form-control" id="name" placeholder=""
                                           v-model="selectedProduct.name">
                                    <label for="name">Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Description</span>
                                        <textarea v-model="selectedProduct.description" class="form-control"
                                                  aria-label="With textarea"></textarea>
                                    </div>
                                </div>

                                <div class="form-floating mb-3 mt-3">
                                    <input type="number" class="form-control" id="price" placeholder=""
                                           v-model="selectedProduct.price">
                                    <label for="price">Price</label>
                                </div>

                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" id="inputGroupFile" ref="fileInput"
                                           @change="handleFileChange">
                                    <label class="input-group-text" for="inputGroupFile">Upload</label>
                                </div>

                                <div class="d-flex flex-row ">
                                    <div class="d-flex flex-row justify-content-center w-50 ps-3 pe-3">
                                        <button class="btn btn-primary w-100" @click.prevent="updateProduct">Update</button>
                                    </div>

                                    <div class="d-flex flex-row justify-content-center w-50 ps-3 pe-3">
                                        <button class="btn btn-danger w-100" @click.prevent="deleteProduct">Delete</button>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <h3>Add more functionality</h3>

                        <div class="d-flex flex-row">
                            <div class="w-25 me-3">
                                <div class="d-flex flex-column">
                                    <button class="btn btn-primary w-100 mb-3" @click.prevent="showProductAddCategory">
                                        Add category
                                    </button>
                                    <template v-for="category in productCategories">
                                        <div class="sidebar-item mb-1 text-dark"
                                             @click.prevent="selectProductCategory(category)"> {{ category.name }}
                                            <div class="border border-3 border-bottom w-100"></div>
                                        </div>
                                        <template v-for="product in category.items">
                                            <div class="sidebar-sub-item mb-1 text-dark"
                                                 @click.prevent="selectProductCategoryItem(product, category)">
                                                {{ product.name }}
                                                <div class="border border-1 border-bottom w-100"></div>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                            </div>
                            <div class="w-75">
                                <template v-if="whatProductSelected === 'addCategory'">
                                    <div class="form-floating mb-3 mt-3">
                                        <input type="text" class="form-control" id="productCategoryName" placeholder="">
                                        <label for="productCategoryName">Name</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="typeSelect" aria-label="Select product">
                                            <option :value="'Single'" selected>single</option>
                                            <option :value="'Multi'">multi</option>
                                        </select>
                                        <label for="typeSelect">Select type</label>
                                    </div>
                                    <div class="form-floating mb-3 mt-3">
                                        <input type="number" class="form-control" id="countEl" placeholder="">
                                        <label for="countEl">Count</label>
                                    </div>
                                    <button class="btn btn-primary w-100 mb-3" @click.prevent="addProductCategory">Add
                                    </button>
                                </template>
                                <template v-else-if="whatProductSelected === 'category'">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="productSelect" aria-label="Select product">
                                            <template v-for="(product, index) in storeProducts">
                                                <option :value="product.id" :selected="index === 0">{{ product.name }}
                                                </option>
                                            </template>
                                        </select>
                                        <label for="productSelect">Select product</label>
                                    </div>
                                    <button class="btn btn-primary w-100 mb-3"
                                            @click.prevent="addItemToProductCategory">Add product
                                    </button>
                                    <button class="btn btn-danger w-100 mb-3"
                                            @click.prevent="deleteCategoryFromProduct">Delete category
                                    </button>
                                </template>
                                <template v-else-if="whatProductSelected === 'item'">
                                    <div class="me-3 mb-3">
                                        <img :src="'/storage/images/products/' + selectedProductItem.imagePath"
                                             alt="Image"
                                             class="img" style="max-height: 450px">
                                    </div>
                                    <div class="mb-2">{{ selectedProductItem.name }}</div>
                                    <div class="mb-2">{{ selectedProductItem.description }}</div>
                                    <div class="mb-2">{{ selectedProductItem.price }}</div>
                                    <button class="btn btn-danger w-100 mb-3"
                                            @click.prevent="deleteItemFromProductCategory">Delete
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
.sidebar-item {
    background-color: rgba(0, 0, 0, 0); /* Прозрачный цвет слоя */
    transition: background-color 0.3s ease;
}

.img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    object-position: center;
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
</style>
