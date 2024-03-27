<script>
export default {
    name: "Categories",

    data() {
        return {
            categories: null,
            maxPath: 0,
            name: null,
            isOpen: [],

            isMainAdded: null,
            selectedCategory: null,
        }
    },

    mounted() {
        this.getCategories()
        this.setDefaultSelectedCategory()
    },

    updated() {
        this.setDefaultSelectedCategory()
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

        selectCategoryForAddCategory(id) {
            this.isMainAdded = false
            const modalOpenButton = document.getElementById('openModalCategory');
            modalOpenButton.click();
        },

        selectCategoryForAddProduct(id) {
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
            axios.post('/api/store/add/mainCategory', formData).then(res => {
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

            axios.post('/api/store/add/subCategory', formData).then(res => {
                this.getCategories()
                const closeBtn = document.getElementById('btnCloseCategory');
                closeBtn.click();
            })
        },

        clickItem(category) {
            this.selectedCategory = category
        },
        toggleChildVisibility(id) {
            const childsBlock =  document.getElementById('childs_' + id);
            if (childsBlock.classList.contains('d-none')) {
                childsBlock.classList.remove('d-none');
                childsBlock.classList.add('d-block');
            } else {
                childsBlock.classList.remove('d-block');
                childsBlock.classList.add('d-none');
            }
        },

        setDefaultSelectedCategory() {
            if(!this.selectedCategory && this.categories) {
                this.selectedCategory = this.categories[0]
            }
        }
    },
}
</script>

<template>
    <main class="d-flex flex-row">
        <a id="openModalCategory" href="#" class="d-none" data-bs-target="#ModalToggleCategory" data-bs-toggle="modal">Add
            root category</a>
        <a id="openModalProduct" href="#" class="d-none" data-bs-target="#ModalToggleProduct" data-bs-toggle="modal">Add
            product</a>
        <div class="w-25">
            <div class="d-flex flex-column align-self-center">
                <a href="#" @click.prevent="openAddMainCategory()" class="btn btn-primary w-100 mb-2">Add category</a>
                <template v-for="category in categories" :key="category.id">
                    <template v-if="category.childs">
                        <div class="sidebar-item p-2 w-100 d-flex flex-row justify-content-between">
                            <div class="text-light"  @click.prevent="clickItem(category)">{{category.name}}</div>
                            <div class="btn btn-light" @click.prevent="toggleChildVisibility(category.id)"></div>
                        </div>
                        <div :id="'childs_' + category.id" class="d-none">
                            <template v-for="child in category.childs" :key="child.id">
                                <div class="sidebar-sub-item p-2 w-100 d-flex flex-row justify-content-between" @click.prevent="clickItem(child)">
                                    <div class="text-light">{{child.name}}</div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <div class="sidebar-item p-2 w-100 d-flex flex-row justify-content-between" @click.prevent="clickItem(category)">
                            <div class="text-light">{{category.name}}</div>
                        </div>
                    </template>
                </template>
            </div>
        </div>
        <div v-if="selectedCategory" class="w-75 ms-3">
            <div class="d-flex flex-column mt-3">
                <h3 class="m-3 text-light text-center">{{selectedCategory.name}}</h3>
                <a href="#" @click.prevent="selectCategoryForAddCategory(selectedCategory.id)" class="btn btn-primary w-100 mb-2">Add category</a>
                <a v-if="selectedCategory && selectedCategory.forProducts" href="#" @click.prevent="selectCategoryForAddProduct(selectedCategory.id)" class="btn btn-primary w-100">Add product</a>
            </div>
        </div>

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
                        <template v-if="selectedCategory">
                            <div> Parent id: {{ selectedCategory }}</div>
                        </template>

                        <main class="align-self-center">
                            <div class="form-floating mb-3 mt-3">
                                <input type="text" class="form-control" id="name" placeholder="" v-model="name">
                                <label for="name">Name</label>
                            </div>
                        </main>

                    </div>
                    <div class="modal-footer">

                        <template v-if="!isMainAdded">
                            <button href="#" class="btn btn-primary w-100" @click.prevent="addSubCategory()">
                                Add subcategory
                            </button>
                        </template>
                        <template v-else>
                            <button class="btn btn-primary w-100" @click.prevent="addMainCategory()">
                                Add category
                            </button>
                        </template>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalToggleProduct" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
             tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Add product</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Hide this modal and show the first with the button below.
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal">Back
                            to first
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </main>
</template>

<style scoped>
    .sidebar-item {
        background-color: lightgray;
    }
    .sidebar-sub-item{
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
