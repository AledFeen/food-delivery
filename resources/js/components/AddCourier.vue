<script>
export default {
    name: 'AddCourier',
    props: ['userId'],

    data() {
        return {
            id:null,
            name: null,
            surname: null,
            phone: null,
            cities: []
        }
    },

    mounted() {
        this.getCities()
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

        addCourier() {

            const userConfirmed = confirm("Are you sure you want to continue?");

            if (userConfirmed) {
                var phonePattern = /^\+?(\d{1,3})?\s?\(?(\d{3})\)?[-.\s]?(\d{3})[-.\s]?(\d{2})[-.\s]?(\d{2})$/;
                var selectElement = document.getElementById("citySelect")
                var selectedOption = selectElement.options[selectElement.selectedIndex].value

                if (this.name && this.surname && this.phone && phonePattern.test(this.phone)) {
                    const formData = new FormData()
                    formData.append('name', this.name)
                    formData.append('surname', this.surname)
                    formData.append('phone', this.phone)
                    formData.append('cityId', selectedOption)
                    formData.append('userId', this.userId)

                    axios.post('/api/admin/courier/add', formData)
                        .then(() => {
                            this.$router.push({ name: 'admin.users'});
                        })
                        .catch(error => {
                            console.error('Error fetching store:', error)
                            window.alert("Error. Try again.");
                        })
                } else alert('You must fill all inputs')

                console.log("Пользователь подтвердил действие.");
            } else {
                console.log("Пользователь отменил действие.");
            }
        }
    }
}
</script>

<template>
    <div class="text-light">Add store</div>
    <main class="align-self-center">

        <h1 class="text-light mt-3">Add post</h1>
        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="" v-model="name">
            <label for="name">Name</label>
        </div>

        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" id="surname" placeholder="" v-model="surname">
            <label for="name">Surname</label>
        </div>

        <div class="form-floating mb-3 mt-3">
            <input type="number" class="form-control" id="phone" placeholder="" v-model="phone">
            <label for="name">Phone</label>
        </div>

        <div class="form-floating mb-3">
            <select class="form-select" id="citySelect" aria-label="Select city">
                <template v-for="(city, index) in cities">
                    <option :value="city.id" :selected="index === 0">{{city.name}}</option>
                </template>
            </select>
            <label for="typeSelect">Select category</label>
        </div>
        <input @click.prevent="addCourier" type="submit" class="w-100 btn btn-lg btn-light btn-outline-dark mb-2 mt-2" value="Add"/>
        <div class="d-flex justify-content-end">
            <router-link :to="{name: 'admin.users'}" class="btn btn-success">Back to users</router-link>
        </div>
    </main>
</template>

<style scoped>

</style>
