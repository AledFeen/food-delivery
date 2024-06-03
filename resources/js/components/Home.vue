<script>
export default {
    name: "Home",
    data() {
        return {
            cities: [],
            selectedCity: null,
            stores: null,
            allStores: [],
            categories: [],
            types: [],
            selectedFilter: null,
            pagination: [],
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

        getStores(page = 1) {
            if(this.selectedCity) {
                axios.get('/api/stores', {
                    params: { city_id: this.selectedCity.id, page: page }
                }).then(res => {
                    this.stores = res.data.pagination.data
                    this.allStores = res.data.pagination.data
                    this.pagination = res.data.pagination
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
            localStorage.setItem('deliveryPrice', this.selectedCity.price)
            this.$router.push({ name: 'store', params: { storeId: id } });
        }
    }
}
</script>

<template>
    <template v-if="!selectedCity">
        <h3 class="text-dark text-center mt-3">Select your city</h3>
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
        <div class="text-dark text-center mt-2">You changed city
            <a id="openModalCategory" href="#" class="text-dark" data-bs-target="#ModalToggleCity" data-bs-toggle="modal">{{selectedCity.name}}</a>
        </div>
    </template>

    <template v-if="stores">
        <div class="d-flex flex-row">
            <div class="w-25">
                <div class="d-flex flex-column text-center">
                    <div :id="'clear_filter'" @click.prevent="selectFilter(15,null)" class="btn btn-danger mb-3 w-75">Clear</div>
                    <template v-if="types">
                        <h4 class="text-dark text-start">Type shop</h4>
                            <template v-for="type in types">
                                <div :id="'filter_type_' + type.name" @click.prevent="selectFilter(0,type)" class="filter text-dark p-1 m-1 w-75 rounded-1">{{type.name}}</div>
                                <div class="border border-1 border-bottom w-75"></div>
                            </template>
                    </template>
                    <template v-if="categories">
                        <h4 class="text-dark text-start mt-3">Category</h4>
                        <div class="d-flex flex-column">
                            <template v-for="category in categories">
                                <div :id="'filter_category_' + category.name" @click.prevent="selectFilter(1,category)" class="filter text-dark p-1 m-1 w-75 rounded-1">{{category.name}}</div>
                                <div class="border border-1 border-bottom w-75"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            <div class="w-75">
                <!-- Stores -->
                <div class=" mt-3 d-flex flex-row flex-wrap" style="height: 40%">
                    <template v-for="store in stores">
                        <div class="col-4 d-flex flex-column flex-wrap ms-2 me-2 rounded-3 border border-1 h-100">
                            <div class="image-container rounded-3 h-75">
                                <img :src="'/storage/images/stores/' + store.image" class="w-100 rounded-3 img" alt="Image">
                                <div @click.prevent="selectStore(store.id)" class="overlay"></div>
                            </div>
                            <h5 class="pt-3 text-center text-dark">{{store.name}}</h5>
                        </div>
                    </template>
                </div>
                <!-- Pagination -->
                <div v-if="pagination.links.length > 1" class="d-flex flex-row justify-content-center mt-5">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li v-if="pagination.current_page !== 1" class="page-item">
                                <a @click.prevent="getStores(pagination.current_page - 1)" class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li v-for="link in pagination.links" class="page-item">
                                <template v-if="Number(link.label) &&
                                    (pagination.current_page - link.label < 2 &&
                                    pagination.current_page - link.label > -2) ||
                                    Number(link.label) === 1 || Number(link.label) === pagination.last_page">
                                    <a @click.prevent="getStores(link.label)" :class="link.active ? 'active' : ''" class="page-link" href="#">{{link.label}}</a>
                                </template>
                            </li>
                            <li v-if="pagination.current_page !== pagination.last_page" class="page-item">
                                <a  @click.prevent="getStores(pagination.current_page + 1)" class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </template>

    <!-- Modal 1 -->
    <div class="modal fade" id="ModalToggleCity" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Select your city</h1>
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

    .filter {
        background-color: rgba(0, 0, 0, 0);
        transition: background-color 0.3s ease;
    }

    .overlay:hover {
        background-color: rgba(0, 0, 0, 0.5); /* Затемненный цвет слоя при наведении */
    }

    .filter:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }




</style>
