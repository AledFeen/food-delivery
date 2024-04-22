<script>
export default {
    name: "Store profile",

    data() {
        return {
            store: null,
            types: null,
            categories: null,
            image: null,
            cities: null,
            storeCities: null
        }
    },

    mounted() {
        this.getTypes()
        this.getStore()
        this.getCategories()
        this.getCities()
        this.getStoreCities()
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

        getCities() {
            axios.get('/api/cities')
                .then(res => {
                    this.cities = res.data.cities
                }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        getStoreCities() {
            axios.get('/api/store/cities')
                .then(res => {
                    this.storeCities = res.data.cities
                }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        addCity() {
            var selectElement = document.getElementById("citySelect")
            var selectedOption = selectElement.options[selectElement.selectedIndex].value

            if (this.storeCities.some(city => city.id === selectedOption)) {
                window.alert('Це місто вже додано')
            } else {
                const formData = new FormData()
                formData.append('city_id', selectedOption)
                axios.post('/api/store/add/city', formData)
                    .then(res => {
                        this.getStoreCities()
                    })
                    .catch(error => {
                        console.error('Error updating store:', error)
                        window.alert("Error. Try again.");
                    })
            }
        },
        deleteCity(id) {
            axios.delete(`/api/store/city/${id}`)
                .then(res => {
                this.getStoreCities()
                })
                .catch(error => {
                    console.error('Error updating store:', error)
                    window.alert("Error. Try again.");
                })
        }
    }
}
</script>

<template>
    <main v-if="store" class="align-self-center mt-4 mb-4">

        <h1 class="text-dark mt-3">Редагувати профіль магазину</h1>

        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="" v-if="store" v-model="store.name">
            <label for="name">Name</label>
        </div>

        <div class="form-floating mb-3">
            <div v-if="store" class="input-group">
                <span class="input-group-text">Description</span>
                <textarea v-model="store.description" class="form-control" aria-label="With textarea"></textarea>
            </div>
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
            <div class="w-25"><img v-if="store" :src="'/storage/images/stores/' + store.image" class="w-50" alt="Image">
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="file" class="form-control" id="inputGroupFile" ref="fileInput" @change="handleFileChange">
            <label class="input-group-text" for="inputGroupFile">Upload</label>
        </div>

        <input @click.prevent="updateProfile()" type="submit"
               class="w-100 btn btn-lg btn-light btn-outline-dark mb-2 mt-2" value="Оновити"/>

        <h3 class="text-center text-light">Міста</h3>

        <div class="d-flex flex-row">
            <div class="w-50 me-3">
                <div class="form-floating mb-3">
                    <select class="form-select" id="citySelect" aria-label="Floating label select example">
                        <template v-for="(city, index) in cities">
                            <option :value="city.id">{{ city.name }}</option>
                        </template>
                    </select>
                    <label for="citySelect">Select category</label>
                </div>
                <input @click.prevent="addCity" type="submit" class="w-100 btn btn-lg btn-light btn-outline-dark mb-2 mt-2" value="Додати місто"/>
            </div>
            <div class="w-50">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Country</th>
                        <th scope="col">Button</th>

                    </tr>
                    </thead>
                    <tbody>
                        <template v-for="city in storeCities">
                            <tr>
                                <th scope="row">{{city.country}}</th>
                                <td>{{city.name}}</td>
                                <td><input @click.prevent="deleteCity(city.id)" type="submit" class="w-100 btn btn btn-danger btn-outline-dark mb-2 mt-2" value="Видалити"/></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</template>

<style scoped>

</style>
