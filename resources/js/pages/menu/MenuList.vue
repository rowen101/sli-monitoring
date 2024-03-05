<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import MenuItemList from "./MenuItemList.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import { ContentLoader } from 'vue-content-loader'

const toastr = useToastr();
const lists = ref({ data: [] });
const menuOptionlist = ref({ data: [] });


const isloading = ref(false);
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const authUserStore = useAuthUserStore();
const selectedStatus = ref(null);
const selectedParentID = ref();

const getItems = (page = 1) => {
    isloading.value = true
    axios
        .get(`/api/menulist?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            isloading.value = false
            lists.value = response.data;
            selectedItems.value = [];
            selectAll.value = false;

        });
};

const parentMenus =() =>{
    axios
        .get(`/api/GetParentId`)
        .then((response) => {
            menuOptionlist.value = response.data;
        })
        .catch((error) => {
            console.error(error);
        });

}
const createUserSchema = yup.object({
    menu_title: yup.string().required(),
    parent_id: yup.string().required(),
    sort_order: yup.string().required(),
    menu_icon: yup.string().required(),
    menu_route: yup.string().required(),

});

const editUserSchema = yup.object({
    menu_title: yup.string().required(),
    parent_id: yup.string().required(),
    sort_order: yup.string().required(),
    menu_icon: yup.string().required(),
    menu_route: yup.string().required(),
});

const createData = (values, { resetForm, setErrors }) => {
    axios
        .post("/api/menulist", {
            menu_title: values.menu_title,
            parent_id: selectedParentID.value,
            menu_icon:values.menu_icon,
            menu_route: values.menu_route,
            sort_order: values.sort_order,
            is_active: values.is_active
        })
        .then((response) => {
            lists.value.data.unshift(response.data);
            $("#FormModal").modal("hide");
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
    $("#FormModal").modal("show");
};

const editUser = (item) => {

    editing.value = true;
    form.value.resetForm();
    $("#FormModal").modal("show");
     formValues.value = {
        menu_id: item.menu_id,
        menu_title: item.menu_title,
        menu_icon: item.menu_icon,
        parent_id: item.parent_id,
        menu_route: item.menu_route,
        sort_order: item.sort_order,
        is_active: item.is_active,
    };
    selectedParentID.value = item.parent_id;

};

const viewItem = (item) => {
    editing.value = true;
    form.value.resetForm();
    $("#FormModalView").modal("show");
    formValues.value = {
        menu_id: item.menu_id,
        menu_title: item.menu_title,
        menu_icon: item.menu_icon,
        parent_id: item.parent_id,
        menu_route: item.menu_route,
        sort_order: item.sort_order,
        is_active: item.is_active,

    };
};

const updateData = (values, { setErrors }) => {
    axios
        .post("/api/menulist", values)
        .then((response) => {

          getItems();
            $("#FormModal").modal("hide");
            toastr.success("successfully Updated!");
        })
        .catch((error) => {
            setErrors(error.response.data.errors);
            console.log(error);
        });
};

const handleSubmit = (values, actions) => {
    // console.log(actions);
    if (editing.value) {
        updateData(values, actions);
    } else {
        createData(values, actions);
    }
};

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
    $("#deleteClientModal").modal("show");
};

const deleteUser = () => {
    axios
        .delete(`menulist/${userIdBeingDeleted.value}`)
        .then(() => {
            $("#deleteClientModal").modal("hide");
            toastr.success("Client deleted successfully!");
            lists.value.data = lists.value.data.filter(
                (user) => user.id !== userIdBeingDeleted.value
            );
        });
};

const bulkDelete = () => {
    axios
        .delete("menulist", {
            data: {
                ids: selectedItems.value,
            },
        })
        .then((response) => {
            lists.value.data = lists.value.data.filter(
                (user) => !selectedItems.value.includes(user.id)
            );
            selectedItems.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllUsers = () => {
    if (selectAll.value) {
        selectedItems.value = lists.value.data.map((user) => user.id);
    } else {
        selectedItems.value = [];
    }
    console.log(selectedItems.value);
};

const updateStatus = (status) => {
    selectedStatus.value = status;
};

watch(
    searchQuery,
    debounce(() => {
        getItems();
    }, 300)
);

onMounted(() => {
    getItems();
    parentMenus();
});
</script>

<template>


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button
                        @click="addUser"
                        type="button"
                        class="mb-2 btn btn-primary"
                    >
                        <i class="fa fa-plus-circle mr-1"></i>
                        Menu
                    </button>
                    <div v-if="selectedItems.length > 0">
                        <button
                            @click="bulkDelete"
                            type="button"
                            class="ml-2 mb-2 btn btn-danger"
                        >
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2"
                            >Selected
                            {{ selectedItems.length }} techrecomm</span
                        >
                    </div>
                </div>
                <div>
                    <input
                        type="text"
                        v-model="searchQuery"
                        class="form-control"
                        placeholder="Search..."
                    />
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <ContentLoader v-if="isloading" viewBox="0 0 250 110">
                    <rect x="0" y="0" rx="3" ry="3" width="250" height="10" />
                    <rect x="0" y="20" rx="3" ry="3" width="250" height="10" />
                    <rect x="0" y="40" rx="3" ry="3" width="250" height="10" />
                    <rect x="0" y="60" rx="3" ry="3" width="250" height="10" />
                    </ContentLoader>
                   <div class="table-responsive">
                    <table class="table table-bordered table-sm">
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
                                <th>Title</th>
                                <th>Parent Menu</th>
                                <th>Route</th>
                                <th>Icon</th>
                                <th>Sort</th>
                                <th>Active</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="lists.data.length > 0">
                            <MenuItemList
                                v-for="(item, index) in lists.data"
                                :key="item.id"
                                :item="item"
                                :index="index"
                                @edit-user="editUser"
                                @show-item="viewItem"
                                @confirm-user-deletion="confirmItemDeletion"
                                @toggle-selection="toggleSelection"
                                :select-all="selectAll"
                            />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="9" class="text-center">
                                    No results found...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                   </div>
                </div>
            </div>
            <Bootstrap4Pagination
                :data="lists"
                @pagination-change-page="getItems"
            />
        </div>
    </div>



    <div
        class="modal fade"
        id="FormModal"
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
                        <span v-if="editing">Edit Menu</span>
                        <span v-else>Add Menu</span>
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

                    v-slot="{ errors }"
                    :initial-values="formValues"
                >
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-12">
                                    <Field
                                        type="hidden"
                                        name="created_by"
                                        id="created_by"
                                        v-model="authUserStore.user.id"
                                    />


                                    <div class="form-group">
                                        <label for="user">Menu Title</label>
                                        <Field
                                            name="menu_title"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.menu_title,
                                            }"
                                            id="menu_title"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Menu title"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.menu_title
                                        }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="department"
                                            >Parent Menu</label
                                        >
                                        <select  class="form-control" id="parentMenu" v-model="selectedParentID">
                                                        <option value=0>None</option>
                                                        <option v-for="parent in menuOptionlist" :key="parent.menu_id" :value="parent.menu_id">
                                                            {{ parent.menu_title }}
                                                        </option>
                                                    </select>

                                    </div>

                                    <div class="form-group">
                                        <label for="warehouse">Icon</label>
                                        <Field
                                            name="menu_icon"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.menu_icon,
                                            }"
                                            id="menu_icon"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Menu Icon"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.menu_icon
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user">Route</label>
                                        <Field
                                            name="menu_route"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.menu_route,
                                            }"
                                            id="menu_route"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Route"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.menu_route
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user">Sort</label>
                                        <Field
                                            name="sort_order"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.sort_order,
                                            }"
                                            id="sort_order"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Sort Order"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.sort_order
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user">Active</label>
                                        <Field

                                            name="is_active"
                                            type="checkbox"


                                        />

                                    </div>
                                </div>

                            </div>
                        </div>
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
        id="deleteClientModal"
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
                        <span>Delete Record</span>
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
                    <h5>Are you sure you want to delete this record ?</h5>
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
