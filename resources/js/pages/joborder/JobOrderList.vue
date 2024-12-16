<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import JobItemList from "./JobOrderAction.vue";
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

// const updateIsActive = (checked) => {
//         form.is_active = checked ? 1 : 0;

//     }
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
const editAction = (item) => {

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
                        @click="$router.push('/job-order-request-create')"
                        type="button"
                        class="mb-2 btn btn-primary"
                    >
                        <i class="fa fa-plus-circle mr-1"></i>
                        Menu
                    </button> -->
                    <router-link :class="'mb-2 btn btn-primary'" :to="{ name: 'Job Order Create' }">
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
                                    <th>Site</th>
                                    <th>Order Number</th>
                                    <th>End User</th>
                                    <th>Requested Date</th>
                                    <th>Needed Date</th>
                                    <th>Commitment Date</th>
                                    <th>Status</th>
                                    <th>Createdby</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="lists.data.length > 0">
                                <tr
                                v-for="(item, index) in lists"
                                :key="item.id"
                                :item="item"
                                :index="index"
                                @confirm-deletion="confirmDeletion"
                                @toggle-selection="toggleSelection"
                                :select-all="selectAll"
                            >
                            <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.site_name }}</td>
                            <td>{{ item.job_order_number }}</td>
                            <td>{{ item.end_user }}</td>
                        <td>{{ item.date_needed }}</td>
                        <td>{{ item.date_needed }}</td>
                            <td>{{ item.commitment_date }}</td>
                            <td style="text-align: center;">{{ item.status }}</td>
                            <td>{{ item.created_at }}</td>
                            <td class="text-center">
                            <button class="btn btn-sm bg-primary" @click.prevent="$emit('editAction', item)">
                                <i class="fa fa-edit" ></i>
                            </button>&nbsp;
                            <button class="btn btn-sm bg-danger" @click.prevent="$emit('confirmDeletion', item.menu_id)">
                                <i class="fa fa-trash "></i>
                            </button>

                            </td>
                        </tr>
                                <!-- <JobItemList
                                    v-for="(item, index) in lists.data"
                                    :key="item.id"
                                    :item="item"
                                    :index="index"
                                    @edit-menu="editAction"
                                    @confirm-deletion="confirmDeletion"
                                    @toggle-selection="toggleSelection"
                                    :select-all="selectAll"
                                /> -->
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
