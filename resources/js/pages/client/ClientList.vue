<script setup>
import axios from 'axios';
import { ref, onMounted, reactive, watch } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import UserListItem from './ClientListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr();
const lists = ref({'data': []});
const editing = ref(false);
const formValues = ref();
const form = ref(null);

const getItems = (page = 1) => {
    axios.get(`/api/clients?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
    .then((response) => {
        lists.value = response.data;
        selectedItems.value = [];
        selectAll.value = false;
    })
}

const createUserSchema = yup.object({
    first_name: yup.string().required(),
    last_name: yup.string().required(),
    email: yup.string().email().required()

});

const editUserSchema = yup.object({
    first_name: yup.string().required(),
    last_name: yup.string().required(),
    email: yup.string().email().required(),

});

const createData = (values, { resetForm, setErrors }) => {
    axios.post('/api/clients', values)
        .then((response) => {
            lists.value.data.unshift(response.data);
            $('#clientFormModal').modal('hide');
            resetForm();
            toastr.success('User created successfully!');
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        })
};

const addUser = () => {
    editing.value = false;
    $('#clientFormModal').modal('show');
};

const editUser = (item) => {
    editing.value = true;
    form.value.resetForm();
    $('#clientFormModal').modal('show');
    formValues.value = {
        id: item.id,
        first_name: item.first_name,
        last_name: item.last_name,
        email: item.email,
    };
};

const updateData = (values, { setErrors }) => {
    axios.put('/api/clients/' + formValues.value.id, values)
        .then((response) => {
            const index = lists.value.data.findIndex(user => user.id === response.data.id);
            lists.value.data[index] = response.data;
            $('#clientFormModal').modal('hide');
            toastr.success('Client updated successfully!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
            console.log(error);
        });
}

const handleSubmit = (values, actions) => {
    // console.log(actions);
    if (editing.value) {
        updateData(values, actions);
    } else {
        createData(values, actions);
    }
}

const searchQuery = ref(null);

const selectedItems = ref([]);
const toggleSelection = (user) => {
    const index = selectedItems.value.indexOf(user.id);
    if (index === -1) {
        selectedItems.value.push(user.id);
    } else {
        selectedItems.value.splice(index, 1);
    }
    console.log(selectedItems.value);
};

const userIdBeingDeleted = ref(null);
const confirmItemDeletion = (id) => {
    userIdBeingDeleted.value = id;
    $('#deleteClientModal').modal('show');
};

const deleteUser = () => {
    axios.delete(`/api/clients/${userIdBeingDeleted.value}`)
    .then(() => {
        $('#deleteClientModal').modal('hide');
        toastr.success('Client deleted successfully!');
        lists.value.data = lists.value.data.filter(user => user.id !== userIdBeingDeleted.value);
    });
};

const bulkDelete = () => {
    axios.delete('/api/clients', {
        data: {
            ids: selectedItems.value
        }
    })
    .then(response => {
        lists.value.data = lists.value.data.filter(user => !selectedItems.value.includes(user.id));
        selectedItems.value = [];
        selectAll.value = false;
        toastr.success(response.data.message);
    });
};

const selectAll = ref(false);
const selectAllUsers = () => {
    if (selectAll.value) {
        selectedItems.value = lists.value.data.map(user => user.id);
    } else {
        selectedItems.value = [];
    }
    console.log(selectedItems.value);
}

watch(searchQuery, debounce(() => {
    getItems();
}, 300));

onMounted(() => {
    getItems();
});
</script>

<template>
   


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button @click="addUser" type="button" class="mb-2 btn btn-primary">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add New Client
                    </button>
                    <div v-if="selectedItems.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedItems.length }} clients</span>
                    </div>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers" /></th>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="lists.data.length > 0">
                            <UserListItem v-for="(item, index) in lists.data"
                                :key="item.id"
                                :item=item :index=index
                                @edit-user="editUser"
                                @confirm-user-deletion="confirmItemDeletion"
                                @toggle-selection="toggleSelection"
                                :select-all="selectAll" />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No results found...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="lists" @pagination-change-page="getItems" />
        </div>
    </div>

    <div class="modal fade" id="clientFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Client</span>
                        <span v-else>Add New Client</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema"
                    v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fist_name">First Name</label>
                            <Field name="first_name" type="text" class="form-control" :class="{ 'is-invalid': errors.first_name }"
                                id="name" aria-describedby="nameHelp" placeholder="Enter First Name" />
                            <span class="invalid-feedback">{{ errors.first_name }}</span>
                        </div>

                         <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <Field name="last_name" type="text" class="form-control" :class="{ 'is-invalid': errors.last_name }"
                                id="last_name" aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.last_name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control "
                                :class="{ 'is-invalid': errors.email }" id="email" aria-describedby="nameHelp"
                                placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteClientModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteUser" type="button" class="btn btn-primary">Delete User</button>
                </div>
            </div>
        </div>
    </div>
</template>
