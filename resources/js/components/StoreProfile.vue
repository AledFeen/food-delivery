<script>
export default {
    name: "Store profile",

    data() {
        return {
            store: null,
            types: null,
            categories: null
        }
    },

    mounted() {
        this.getTypes()
        this.getStore()
        this.getCategories()
    },

    methods: {
        getStore() {
            axios.get('/api/user/store').then(res => {
                if (res.data.response === false) {
                    window.alert("У вас немає доступу до цієї сторінки");
                    this.$router.push({name: 'home.index'})
                } else {
                    this.store = res.data.store
                    console.log(res.data.store)
                }
            }).catch(err => {
                console.log(err)
            })
        },

        getTypes() {
            axios.get('/api/types/get').then(res => {
                this.types = res.data.types
            }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        getCategories() {
            axios.get('/api/categories/get').then(res => {
                this.categories = res.data.categories
            }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        updateProfile() {
            const formData = new FormData()
            formData.append('name', this.store.name)
            formData.append('description', this.store.description)
            formData.append('user_id', this.store.userId)
            formData.append('image', this.image)
            var selectElement = document.getElementById("typeSelect")
            var selectedOption = selectElement.options[selectElement.selectedIndex].value
            formData.append('type_store', selectedOption)
            selectElement = document.getElementById("categoriesSelect")
            selectedOption = selectElement.options[selectElement.selectedIndex].value
            formData.append('store_category', selectedOption)


            axios.post('/api/user/store/updateProfile', formData)
                .then(res => {
                    this.getTypes()
                    this.getCategories()
                    this.getStore()
                })
                .catch(error => {
                    console.error('Error updating store:', error)
                    window.alert("Error. Try again.");
                })
        },

        handleFileChange() {
            if (this.$refs.fileInput) {
                this.image = this.$refs.fileInput.files[0]
            }
        },
    }
}
</script>

<template>
    <main v-if="store" class="align-self-center">

        <h1 class="text-light mt-3">Редагувати профіль магазину</h1>

        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="" v-if="store" v-model="store.name">
            <label for="name">Name</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="description" placeholder="" v-if="store"
                   v-model="store.description">
            <label for="description">Description</label>
        </div>

        <div class="form-floating mb-3">
            <select class="form-select" id="typeSelect" aria-label="Floating label select example">
                <template v-for="(type, index) in types">
                    <option :value="type.name" :selected="type.name === store.type_store">{{ type.name }}</option>
                </template>
            </select>
            <label for="typeSelect">Select types</label>
        </div>

        <div class="form-floating mb-3">
            <select class="form-select" id="categoriesSelect" aria-label="Floating label select example">
                <template v-for="(category, index) in categories">
                    <option :value="category.name" :selected="category.name === store.category">{{ category.name }}</option>
                </template>
            </select>
            <label for="categoriesSelect">Select category</label>
        </div>

        <div class="form-floating mb-3 mt-3">
            <td class="w-25"><img v-if="store" :src="'/storage/images/stores/' + store.image" class="w-50" alt="Image">
            </td>
        </div>

        <div class="input-group mb-3">
            <input type="file" class="form-control" id="inputGroupFile" ref="fileInput" @change="handleFileChange">
            <label class="input-group-text" for="inputGroupFile">Upload</label>
        </div>

        <input @click.prevent="updateProfile()" type="submit"
               class="w-100 btn btn-lg btn-light btn-outline-dark mb-2 mt-2" value="Оновити"/>
    </main>
</template>

<style scoped>

</style>
