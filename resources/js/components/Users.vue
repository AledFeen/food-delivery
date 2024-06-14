<script>
export default {
    name: 'Users component',
    mounted() {
        this.getUsers()
    },
    data() {
        return {
            users: null,
            selectedUser: null,
            hasStore: null,
            isCourier: null
        }
    },
    methods: {
        getUsers() {
            axios.get('/api/admin/users/get')
                .then(response => {
                    if(response.data.response === false) {
                        window.alert("У вас немає доступу до цієї сторінки")
                        this.$router.push({name: 'home.index'})
                    } else {
                        this.users = response.data.users
                        console.log(response.data)
                    }
                })
                .catch(error => {
                    console.error('Error fetching posts:', error)
                })
        },

        selectUser(id, hasStore, isCourier) {
            this.selectedUser = id
            this.isCourier = isCourier
            this.hasStore = hasStore
        },

        isSelected(id) {
            return this.selectedUser === id
        },

        clearSelect() {
            this.selectedUser = null
            this.isCourier = null
            this.hasStore = null
        },

        addStore() {
            if (this.selectedUser) {
                this.$router.push({ name: 'admin.addStore', params: { userId: this.selectedUser } });
            }
        },

        deleteStore() {
            const userConfirmed = confirm("Are you sure you want to continue?");
            if (userConfirmed) {
                axios.delete('/api/admin/store/delete', {
                    data: {
                        userId: this.selectedUser
                    }
                }).catch(error => {
                    alert(error.response.data.message)
                    console.error(error);
                });
            }
        },

        addCourier() {
            if (this.selectedUser) {
                this.$router.push({ name: 'admin.addCourier', params: { userId: this.selectedUser } });
            }
        }
    },
}
</script>

<template>
    <div class="container-md">
        <h1 class="mb-4 text-light">Users</h1>
        <div v-if="!users" class="text-light">Loading...</div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Created at</th>
                <th scope="col">Store</th>
                <th scope="col">Courier</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="user in users">
                <tr>
                    <th scope="row"> {{ user.user.id }}</th>
                    <td @click.prevent="clearSelect()"> {{ user.user.name }}</td>
                    <td @click.prevent="clearSelect()"> {{ user.user.email }}</td>
                    <td @click.prevent="clearSelect()"> {{ user.user.created_at }}</td>
                    <td @click.prevent="clearSelect()"> {{ user.hasStore }} </td>
                    <td @click.prevent="clearSelect()"> {{ user.isCourier }} </td>
                    <td><a href="#" @click.prevent="selectUser(user.user.id, user.hasStore, user.isCourier)" class="btn btn-dark w-100">Edit</a></td>
                </tr>
                <tr :class="isSelected(user.user.id) ? '' : 'd-none'">
                    <th scope="row"></th>
                    <td v-if="!hasStore"><a href="" @click.prevent="addStore" class="btn btn-secondary w-100">Add store</a></td>
                    <td  v-if="hasStore"><a href="" @click.prevent="deleteStore" class="btn btn-danger w-100">Delete store</a></td>
                    <td v-if="!isCourier"><a href="" @click.prevent="addCourier" class="btn btn-secondary w-100">Add courier</a></td>
                </tr>
            </template>
            </tbody>
        </table>

    </div>
</template>

<style scoped>
.blow_text {
    font-weight: 700;
}
</style>
