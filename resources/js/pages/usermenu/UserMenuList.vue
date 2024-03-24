<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import UserMenuListItem from "./UserMenuListItem.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useRoute } from "vue-router";

const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const users = ref({ data: [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const modalname = ref(null);

const selectedMenus = ref([]);
const getUsers = (page = 1) => {
    axios
        .get(`/api/users?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            users.value = response.data;
            selectedUsers.value = [];
            selectAll.value = false;
        });
};

const createUser = (values, { resetForm, setErrors }) => {
    axios
        .post("/api/users", values)
        .then((response) => {
            users.value.data.unshift(response.data);
            $("#userFormModal").modal("hide");
            resetForm();
            toastr.success("User created successfully!");
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const addUser = () => {
    editing.value = false;
    $("#userFormModal").modal("show");
};

const editUser = (user) => {
    axios
       .get("/api/usermenu", {
            params: {
                user_id: user.id, // replace with the actual user ID
            },
        })

        .then((response) => {
            menulist.value = response.data;
            console.log(response.data);
        })
        .catch((error) => {
            console.log(error);
        });
    editing.value = true;
    form.value.resetForm();
    $("#userFormModal").modal("show");
    formValues.value = {
        id: user.id,
        username: user.username,
        email: user.email,
        first_name: user.first_name,
        last_name: user.last_name,
    };
    modalname.value =
        formValues.value.first_name + " " + formValues.value.last_name;
};

const handleSubmit = () => {
    // Prepare the data to be sent to your API
    const postData = {
        user_id: formValues.value.id,
        menu_id: selectedMenus.value,
        // other data properties
    };
    console.log(postData);
    // Make the API call using your preferred method (Axios, fetch, etc.)
    // Example using Axios:
    axios
        .post("/api/usermenu", postData)
        .then((response) => {
            toastr.success("User Menu Save successfully!");
        })
        .catch((error) => {
            setErrors(error.response.data.errors);
        });
};

const searchQuery = ref(null);

const selectedUsers = ref([]);
const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
    console.log(selectedUsers.value);
};

const userIdBeingDeleted = ref(null);
const confirmUserDeletion = (id) => {
    userIdBeingDeleted.value = id;
    $("#deleteUserModal").modal("show");
};

const menulist = ref({ data: [] });

watch(
    searchQuery,
    debounce(() => {
        getUsers();
    }, 300)
);

onMounted(() => {
    getUsers();
    document.title = pageTitle;

});
</script>

<template>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5>User Menu</h5>
                    </div>

                    <div class="card-tools">
                        <input
                            type="text"
                            v-model="searchQuery"
                            class="form-control"
                            placeholder="Search..."
                        />
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <font size="2" >
                        <table
                            class="table table-bordered table-hover table-sm"
                        >
                            <thead>
                                <tr>
                                    <th>
                                        <input
                                            type="checkbox"
                                            v-model="selectAll"
                                            @change="selectAllUsers"
                                        />
                                    </th>
                                    <th style="width: 10px">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registered Date</th>

                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody v-if="users.data.length > 0">
                                <UserMenuListItem
                                    v-for="(user, index) in users.data"
                                    :key="user.id"
                                    :user="user"
                                    :index="index"
                                    @edit-user="editUser"
                                    @confirm-user-deletion="confirmUserDeletion"
                                    @toggle-selection="toggleSelection"
                                    :select-all="selectAll"
                                />
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No results found...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </font>
                    </div>
                </div>
            </div>
            <Bootstrap4Pagination
                :data="users"
                @pagination-change-page="getUsers"
            />
        </div>
    </div>

    <div
        class="modal fade"
        id="userFormModal"
        data-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing"
                            >User Menu for {{ modalname }}</span
                        >
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form
                    ref="form"
                    @submit="handleSubmit"
                    :initial-values="formValues"
                >
                    <div class="modal-body">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-bold">Menu Name</td>
                            </tr>
                            <tr v-for="item in menulist" :key="item.menu_id">
                                <td colspan="5">
                                    <div class="custom-control custom-checkbox">
                                        <input
                                            class="custom-control-input"
                                            type="checkbox"
                                            v-model="selectedMenus"
                                            :value="item.menu_id"
                                            :id="'menu_id_' + item.menu_id"
                                            :checked="item.hasAccess"
                                        />
                                        <label
                                            :for="'menu_id_' + item.menu_id"
                                            class="custom-control-label"
                                        >
                                            {{ item.menu_title }}
                                        </label>
                                    </div>
                                    <table
                                        class="table table-sm"
                                        v-if="
                                            item.submenus &&
                                            item.submenus.length > 0
                                        "
                                    >
                                        <tr
                                            v-for="submenu in item.submenus"
                                            :key="submenu.id"
                                        >
                                            <td class="text-danger">
                                                <div
                                                    class="custom-control custom-checkbox"
                                                >
                                                    <input
                                                        class="custom-control-input"
                                                        type="checkbox"
                                                        v-model="selectedMenus"
                                                        :value="submenu.menu_id"
                                                        :id="
                                                            'menu_id_' +
                                                            submenu.menu_id
                                                        "
                                                    />
                                                    <label
                                                        :for="
                                                            'menu_id_' +
                                                            submenu.menu_id
                                                        "
                                                        class="custom-control-label"
                                                    >
                                                        {{ submenu.menu_title }}
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="deleteUserModal"
        data-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user ?</h5>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>
                    <button
                        @click.prevent="deleteUser"
                        type="button"
                        class="btn btn-primary"
                    >
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
