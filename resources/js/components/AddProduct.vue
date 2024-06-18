<script>
export default {
    name: 'addProduct',

    data() {
        return {
            image: null,
            productName: null,
            productDescription: null,
            productPrice: null,
        }
    },

    methods: {
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
            const categoryId =  localStorage.getItem('currentCategoryId')
            formData.append('category_id', categoryId)
            axios.post('/api/store/add/product', formData)
                .then(response => {
                   localStorage.removeItem('currentCategoryId')
                    this.$emit('productAdded')

                })
                .catch(error => {
                    console.error('Error fetching store:', error)
                    window.alert("Error. Try again. " + error.response.data.message)
                })
        }
    }
}
</script>

<template>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Add product</h1>
                <button id="btnCloseProduct" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-floating mb-3 mt-3">
                    <input type="text" class="form-control" id="add_product_name" placeholder="" v-model="productName">
                    <label for="name">Name</label>
                </div>

                <div class="form-floating mb-3">
                    <div class="input-group">
                        <span class="input-group-text">Description</span>
                        <textarea v-model="productDescription" id="add_product_description" class="form-control"
                                  aria-label="With textarea"></textarea>
                    </div>
                </div>

                <div class="form-floating mb-3 mt-3">
                    <input type="number" class="form-control" id="add_product_price" placeholder="" v-model="productPrice">
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
</template>

<style scoped>

</style>
