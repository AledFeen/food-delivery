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
                element.classList.remove("bg-primary")
            }

            let filteredShops = [];
            if (number === 0) {
                filteredShops = this.allStores.filter(store => store.type_store === filter.name)
                const element = document.getElementById("filter_type_" + filter.name)
                element.classList.add("bg-primary")
                this.selectedFilter = "filter_type_" + filter.name
                this.stores = filteredShops
            } else if (number === 1) {
                filteredShops = this.allStores.filter(store => store.category === filter.name)
                const element = document.getElementById("filter_category_" + filter.name)
                element.classList.add("bg-primary")
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
        <h3 class="text-dark text-center mt-3">Оберіть ваше місто</h3>
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
        <div class="text-dark text-center mt-2">Ви обрали місто
            <a id="openModalCategory" href="#" class="text-dark" data-bs-target="#ModalToggleCity" data-bs-toggle="modal">{{selectedCity.name}}</a>
        </div>
    </template>

    <template v-if="stores">
        <div class="d-flex flex-row">
            <div class="w-25">
                <div class="d-flex flex-column text-center">
                    <div :id="'clear_filter'" @click.prevent="selectFilter(15,null)" class="btn btn-danger mb-3 w-75">Очистити</div>
                    <template v-if="types">
                        <h4 class="text-dark text-start">Тип закладу</h4>
                            <template v-for="type in types">
                                <div :id="'filter_type_' + type.name" @click.prevent="selectFilter(0,type)" class="text-dark p-1 m-1 w-75 rounded-1">{{type.name}}</div>
                                <div class="border border-1 border-bottom w-75"></div>
                            </template>
                    </template>
                    <template v-if="categories">
                        <h4 class="text-dark text-start">Категорія</h4>
                        <div class="d-flex flex-column">
                            <template v-for="category in categories">
                                <div :id="'filter_category_' + category.name" @click.prevent="selectFilter(1,category)" class="text-dark p-1 m-1 w-75 rounded-1">{{category.name}}</div>
                                <div class="border border-1 border-bottom w-75"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            <div class="w-75 mt-3 d-flex flex-row flex-wrap">
                <template v-for="store in stores">
                    <div class="col-4 d-flex flex-column ms-1 me-1 rounded-3 border border-1">
                        <div class="image-container rounded-3 h-75">
                            <img :src="'/storage/images/stores/' + store.image" class="w-100 rounded-3 img" alt="Image">
                            <div @click.prevent="selectStore(store.id)" class="overlay"></div>
                        </div>
                        <h5 class="pt-2 text-center text-dark">{{store.name}}</h5>
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
    .image-container {
        position: relative;
    }

    .img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Сохраняет пропорции изображения, заполняя контейнер */
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
