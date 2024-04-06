<template>
    <div class="text-light">Add store</div>
    <main class="align-self-center">

        <h1 class="text-light mt-3">Add post</h1>
        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="" v-model="label">
            <label for="name">Name</label>
        </div>

        <div class="form-floating mb-3">
            <div class="input-group">
                <span class="input-group-text">Description</span>
                <textarea v-model="description" class="form-control" aria-label="With textarea"></textarea>
            </div>
        </div>

        <div class="form-floating mb-3">
            <select class="form-select" id="typeSelect" aria-label="Floating label select example">
                <template v-for="(type, index) in types">
                    <option :value="type.name" :selected="index === 0">{{type.name}}</option>
                </template>
            </select>
            <label for="typeSelect">Select category</label>
        </div>

        <div class="input-group mb-3">
            <input type="file" class="form-control" id="inputGroupFile" ref="fileInput" @change="handleFileChange">
            <label class="input-group-text" for="inputGroupFile" >Upload</label>
        </div>

        <input @click.prevent="addStore" type="submit" class="w-100 btn btn-lg btn-light btn-outline-dark mb-2 mt-2" value="Add"/>
        <div class="d-flex justify-content-end">
            <router-link :to="{name: 'admin.users'}" class="btn btn-success">Back to users</router-link>
        </div>
    </main>
</template>

<script>
export default {
    name: "Test panel",
    props: ['userId'],
    data() {
        return {
            types: null,
            label:null,
            description:null,
            date:null,
            image: null
        }
    },

    mounted() {
        this.getTypes()
    },

    methods: {
        getTypes(){
            axios.get('/api/types/get').then(res => {
                this.types = res.data.types
                console.log(res.data)
            }).catch(error => {
                console.error('Error fetching posts:', error)
            })
        },

        handleFileChange() {
            if (this.$refs.fileInput) {
                this.image = this.$refs.fileInput.files[0]
            }
        },

        addStore() {
            const formData = new FormData()
            formData.append('name', this.label)
            formData.append('description', this.description)
            formData.append('user_id', this.userId)
            formData.append('image', this.image)
            const selectElement = document.getElementById("typeSelect")
            const selectedOption = selectElement.options[selectElement.selectedIndex].value
            formData.append('type_store', selectedOption)
            axios.post('/api/admin/users/add', formData)
                .then(res => {

                })
                .catch(error => {
                    console.error('Error fetching store:', error)
                    window.alert("Error. Try again.");
                })
        }
    }
}
</script>

<style scoped>
.blow_text{
    font-weight: 700;
}
</style>
