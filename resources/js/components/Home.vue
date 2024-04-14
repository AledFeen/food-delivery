<script>
export default {
    name: "Home",
    data() {
        return {
            cities: null,
            selectedCity: null,
            stores: null,
            allStores: null,
            categories: null,
            types: null,
            selectedFilter: null,
        }
    },

    watch: {
        selectedCity(newValue, oldValue) {
            this.getStores()
        }
    },

    mounted() {
        this.getCities()
        this.getTypes()
        this.getCategories()
    },

    methods: {
        getCities() {
            axios.get('/api/cities')
                .then(res => {
                    this.cities = res.data.cities
                }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        getStores() {
            if(this.selectedCity) {
                axios.get('/api/stores', {
                    params: {city_id: this.selectedCity.id}
                }).then(res => {
                    this.stores = res.data.stores
                    this.allStores = res.data.stores
                }).catch(error => {
                    console.error('Error fetching posts:', error)
                })
            }
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

        selectFilter(number, filter) {

            if(this.selectedFilter) {
                const element = document.getElementById(this.selectedFilter)
                element.classList.remove("bg-dark")
                element.classList.add("bg-black")
            }

            let filteredShops = [];
            if (number === 0) {
                filteredShops = this.allStores.filter(store => store.type_store === filter.name)
                const element = document.getElementById("filter_type_" + filter.name)
                element.classList.remove("bg-black")
                element.classList.add("bg-dark")
                this.selectedFilter = "filter_type_" + filter.name
                this.stores = filteredShops
            } else if (number === 1) {
                filteredShops = this.allStores.filter(store => store.category === filter.name)
                const element = document.getElementById("filter_category_" + filter.name)
                element.classList.remove("bg-black")
                element.classList.add("bg-dark")
                this.selectedFilter = "filter_category_" + filter.name
                this.stores = filteredShops
            } else {
                this.stores = this.allStores
                this.selectedFilter = null
            }
        },

        selectStore(id) {
            localStorage.setItem('basketCity', this.selectedCity.id)
            this.$router.push({ name: 'store', params: { storeId: id } });
        }
    }
}
</script>

<template>
    <template v-if="!selectedCity">
        <h3 class="text-light text-center mt-3">Оберіть ваше місто</h3>
        <div class="d-flex flex-row justify-content-center">
            <select v-model="selectedCity" class="form-select w-25" id="citySelect" aria-label="Floating label select example">
                <option :value="null"></option>
                <template v-for="(city, index) in cities">
                    <option :value="city">{{ city.name }}</option>
                </template>
            </select>
        </div>
    </template>
    <template v-else>
        <div class="text-light text-center">Ви обрали місто
            <a id="openModalCategory" href="#" class="text-light" data-bs-target="#ModalToggleCity" data-bs-toggle="modal">{{selectedCity.name}}</a>
        </div>
    </template>

    <template v-if="stores">
        <div class="d-flex flex-row">
            <div class="w-25">
                <div class="d-flex flex-column">
                    <div :id="'clear_filter'" @click.prevent="selectFilter(15,null)" class="text-light mb-3 bg-black w-75">Очистити</div>
                    <template v-if="types">
                        <h4 class=text-light>Тип закладу</h4>
                            <template v-for="type in types">
                                <div :id="'filter_type_' + type.name" @click.prevent="selectFilter(0,type)" class="text-light mb-3 bg-black w-75">{{type.name}}</div>
                            </template>
                    </template>
                    <template v-if="categories">
                        <h4 class=text-light>Категорія</h4>
                        <div class="d-flex flex-column">
                            <template v-for="category in categories">
                                <div :id="'filter_category_' + category.name" @click.prevent="selectFilter(1,category)" class="text-light mb-3 bg-black w-75">{{category.name}}</div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            <div class="w-75 mt-3 d-flex flex-row flex-wrap">
                <template v-for="store in stores">
                    <div @click.prevent="selectStore(store.id)" class="col-4 d-flex flex-column ms-1 me-1">
                        <img :src="'/storage/images/stores/' + store.image" class="w-100 h-75   " alt="Image">
                        <div class="mt-2 text-center text-light">{{store.name}}</div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- Modal 1 -->
    <div class="modal fade" id="ModalToggleCity" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Оберіть місто</h1>
                    <button id="btnCloseCategory" type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-row justify-content-center">
                        <select v-model="selectedCity" class="form-select w-25" id="citySelect" aria-label="Floating label select example">
                            <template v-for="(city, index) in cities">
                                <option :value="city">{{ city.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
