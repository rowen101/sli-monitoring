<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import MenuItemList from "./JobOrderAction.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore.js";
import ContentLoader from "../../components/ContentLoader.vue";
import FormCheckRadioGroup from '@/Components/FormCheckRadioGroup.vue'
import { useRoute } from "vue-router";

const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const lists = ref({ data: [] });
const menuOptionlist = ref({ data: [] });

const isloading = ref(false);
const editing = ref(false);
const formValues = ref();

const checked = ref(true);
const form = reactive({
    menu_id: "",
    menu_title: "",
    parent_id: "",
    menu_icon: "",
    menu_route: "",
    sort_order: "",
    is_active: "",
    ceated_by: "",
    updated_by: "",
});

const authUserStore = useAuthUserStore();
const selectedStatus = ref(null);
const selectedParentID = ref();

const getItems = (page = 1) => {
    isloading.value = true;
    axios
        .get(`/web/job-request?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            isloading.value = false;
            lists.value = response.data;
            selectedItems.value = [];
            selectAll.value = false;
        });
};


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

const updateIsActive = (checked) => {
        form.is_active = checked ? 1 : 0;

    }
const createData = ( { resetForm, setErrors }) => {
    axios
        .post("/web/menulist", {
            menu_title: form.menu_title,
            parent_id: form.parent_id,
            menu_icon: form.menu_icon,
            menu_route: form.menu_route,
            sort_order: form.sort_order,
            is_active: form.is_active,
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

const editMenu = (item) => {

     editing.value = true;
    form.menu_id = item.menu_id;
    form.menu_title = item.menu_title;
    form.parent_id = item.parent_id;
    form.menu_icon = item.menu_icon;
    form.menu_route = item.menu_route;
    form.sort_order = item.sort_order;
    if (form.is_active === 1) {
        checked.value = true;
    } else {
        checked.value = false;
    }
    form.is_active = item.is_active;
    $("#FormModal").modal("show");
    selectedParentID.value = item.parent_id;
};



const updateData = ( { setErrors }) => {
    axios
        .post("/web/menulist", form)
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

const valIdBeingDeleted = ref(null);

const confirmDeletion = (id) => {
    valIdBeingDeleted.value = id;
    $("#deleteClientModal").modal("show");

};

const deleteUser = () => {
    axios.delete(`/web/menu/${valIdBeingDeleted.value}`).then(() => {
        $("#deleteClientModal").modal("hide");
        toastr.success("Menu deleted successfully!");
        lists.value.data = lists.value.data.filter(
            (val) => val.menu_id !== valIdBeingDeleted.value
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
watch([checked], (val) => {
     form.is_active = val ? 1 : 0;
});

watch(
    searchQuery,
    debounce(() => {
        getItems();
    }, 300)
);

onMounted(() => {
    getItems();
    document.title = pageTitle;

});
</script>

<template>
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <!-- <button
                        @click="addUser"
                        type="button"
                        class="mb-2 btn btn-primary"
                    >
                        <i class="fa fa-plus-circle mr-1"></i>
                        Menu
                    </button> -->
                    <router-link :class="'mb-2 btn btn-primary'" :to="{ name: 'JobOrdercreate' }">
                        <i class="fa fa-plus-circle mr-1"></i>Create
                    </router-link>

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
                            {{ selectedItems.length }} Menu</span
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
                    <ContentLoader v-if="isloading"/>
                    <div class="table-responsive">
                        <font size="2" >
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
                                    @edit-menu="editMenu"
                                    @confirm-deletion="confirmDeletion"
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
                    </font>
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

                    @submit="handleSubmit"
                    v-slot="{ errors }"

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
                                            v-model="form.menu_title"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.menu_title
                                        }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="department"
                                            >Parent Menu</label
                                        >
                                        <select
                                            class="form-control"
                                            id="parentMenu"
                                            v-model="form.parent_id"
                                        >
                                            <option value="0">None</option>
                                            <option
                                                v-for="parent in menuOptionlist"
                                                :key="parent.menu_id"
                                                :value="parent.menu_id"
                                            >
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
                                            v-model="form.menu_icon"
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
                                            v-model="form.menu_route"
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
                                            v-model="form.sort_order"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.sort_order
                                        }}</span>
                                    </div>
                                   <!-- <div class="form-group">
                                        <label for="is_active">Active</label>
                                        <input
                                            name="is_active"
                                            type="checkbox"
                                            id="is_active"
                                            v-model="checked"
                                            @change="updateIsActive"
                                        />
                                    </div> -->
                                    <div class="form-group">
                                        <FormCheckRadioGroup
                                        v-model="form.is_active"
                                        name="is_active"
                                        :options="{ is_active: 'Active' }"
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
