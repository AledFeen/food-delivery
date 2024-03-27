<script>
export default {
    name: "Categories",

    data() {
        return {
            categories: null,
            maxPath: 0,
            selectedCategory: null,
            name: null,

        }
    },

    mounted() {
        this.getCategories()
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

            this.selectedCategory = id;
            console.log(this.selectedCategory);
            const modalOpenButton = document.getElementById('openModalCategory');
            modalOpenButton.click();

        },

        selectCategoryForAddProduct(id) {

            this.selectedCategory = id;
            console.log(this.selectedCategory);

            const modalOpenButton = document.getElementById('openModalProduct');
            modalOpenButton.click();
        },

        openAddMainCategory() {
            this.selectedCategory = null;
            console.log(this.selectedCategory);
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
            formData.append('parent', this.selectedCategory)

            axios.post('/api/store/add/subCategory', formData).then(res => {
                this.getCategories()
                const closeBtn = document.getElementById('btnCloseCategory');
                closeBtn.click();
            })

        }
    },
}
</script>

<template>
    <div class="container-md">

        <a id="openModalCategory" href="#" class="d-none" data-bs-target="#ModalToggleCategory" data-bs-toggle="modal">Add
            root category</a>
        <a id="openModalProduct" href="#" class="d-none" data-bs-target="#ModalToggleProduct" data-bs-toggle="modal">Add
            product</a>
        <h1 class="mb-4 text-light">Сategories</h1>
        <div v-if="!categories" class="text-light">Loading...</div>

        <a href="#" @click.prevent="openAddMainCategory()" class="btn btn-primary w-100 mb-2">Add category</a>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Path</th>
                <th scope="col">Parent</th>
                <th scope="col">Btns</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="category in categories" :key="category.id">
                <tr>
                    <td class="fw-bold">{{ category.id }}</td>
                    <td class="fw-bold">{{ category.name }}</td>
                    <td class="fw-bold">{{ category.path }}</td>
                    <td class="fw-bold">{{ category.parent_id !== null ? category.parent_id : 'null' }}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <a href="#" @click.prevent="selectCategoryForAddCategory(category.id)"
                               class="btn btn-primary w-100 mb-2">Add category</a>
                            <a v-if="category.forProducts" href="#"
                               @click.prevent="selectCategoryForAddProduct(category.id)" class="btn btn-primary w-100">Add
                                product</a>
                        </div>
                    </td>
                </tr>
                <template v-if="category.childs && category.childs.length > 0">
                    <template v-for="child in category.childs" :key="child.id">
                        <tr>
                            <td>{{ child.id }}</td>
                            <td>{{ child.name }}</td>
                            <td>{{ child.path }}</td>
                            <td>{{ child.parent_id }}</td>
                            <td><a href="#" @click.prevent="selectCategoryForAddProduct(category.id)"
                                   class="btn btn-primary w-100">Add product</a></td>
                        </tr>
                    </template>
                </template>
            </template>
            </tbody>
        </table>

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

                        <template v-if="selectedCategory">
                            <button href="#" class="btn btn-primary w-100" @click.prevent="addSubCategory()">
                                Add subcategory
                            </button>
                        </template>
                        <template v-if="!selectedCategory">
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
    </div>
</template>

<style scoped>

</style>
