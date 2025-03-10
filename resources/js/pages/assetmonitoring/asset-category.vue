<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import AssetmItemList from "./AssetmItemList.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import ContentLoader from "../../components/ContentLoader.vue";
import { useRoute, useRouter } from "vue-router";
import FormCheckRadioGroup from '@/Components/FormCheckRadioGroup.vue'
import moment from "moment";
import CategoryList from "./CategoryList.vue";



const route = useRoute(); // Get the current route
const router = useRouter();
const authUserStore = useAuthUserStore();
const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const lists = ref({ data: [] });
const menuOptionlist = ref({ data: [] });

const isloading = ref(false);
const editing = ref(false);
const formValues = ref();

const checked = ref(true);
const form = reactive({
    id: "",
    name:"",
    categorycode:"",
    description:"",
    status: "",
});

const selectedStatus = ref(null);
const selectedParentID = ref();
const listsite = ref();

const getItems = (page = 1) => {
    isloading.value = true;
    axios
        .get(`/web/asset-category/?page=${page}`, {
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
    name: yup.string().required(),
    categorycode: yup.string().required(),
    description: yup.string().required(),

});

const editUserSchema = yup.object({
    name: yup.string().required(),
    categorycode: yup.string().required(),
    description: yup.string().required(),
});

const updateIsActive = (checked) => {
    form.is_active = checked ? 1 : 0;
};
const createData = () => {
    axios
        .post("/web/asset-category/", form)
        .then((response) => {
            $("#FormModal").modal("hide");
            getItems();
            toastr.success(response.data.message);
        })
        .catch((error) => {
            toastr.error(error.data.message);
        });
};

const addUser = () => {
    editing.value = false;
    $("#FormModal").modal("show");
};

const viewData = (item) => {

    form.id = item.id;
    form.name = item.name;
    form.categorycode = item.categorycode;
    form.description = item.description;
    form.ceated_by = item.ceated_by;
    form.updated_by = item.updated_by;
    form.status = item.status;
    $("#ViewRecodeModal").modal("show");
};
const editData = (item) => {
    editing.value = true;
    form.id = item.id;
    form.name = item.name;
    form.categorycode = item.categorycode;
    form.description = item.description;
    form.status = item.status;
    form.ceated_by = item.ceated_by;
    form.updated_by = item.updated_by;

    $("#FormModal").modal("show");

};

const updateData = ({ setErrors }) => {
    axios
        .post("/web/asset-category/", form)
        .then((response) => {
            getItems();
            $("#FormModal").modal("hide");
            toastr.success("Data successfully Updated!");
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

const deleteData = () => {
    axios
        .delete(`/web/asset-category/${valIdBeingDeleted.value}`)
        .then((response) => {
            if (data.message === 'This item is in use. Delete canceled.') {
                toastr.error('This item is in use. Delete canceled.');
    } else if (data.message === 'Data successfully deleted') {
        $("#deleteClientModal").modal("hide");
            toastr.success(response.data.message);
            getItems();
    } else {
        toastr.error('Data not found');
    }

        });
};

const bulkPrint = () => {
    window.print();
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

    }, 300)
);

onMounted(() => {
    getItems();
    document.title = `ESSWMS - ${pageTitle}`;

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
                        Asset Category
                    </button>
                    <div v-if="selectedItems.length > 0">
                        <button
                            @click="bulkPrint"
                            type="button"
                            class="ml-2 mb-2 btn btn-primary"
                        >
                            <i class="fa fa-print mr-1"></i>
                            Print Selected
                        </button>
                        <span class="ml-2"
                            >Selected {{ selectedItems.length }} Record</span
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
                    <ContentLoader v-if="isloading" />
                    <div class="table-responsive">
                        <font size="2">
                            <table
                                class="table table-bordered table-sm table-striped hover"
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
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Createdby</th>
                                        <th>CreatedAt</th>
                                        <th>Updatedby</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="lists.data.length > 0">
                                    <CategoryList
                                        v-for="(item, index) in lists.data"
                                        :key="item.id"
                                        :item="item"
                                        :index="index"
                                        :selectAll="selectAll"
                                        @toggleSelection="toggleSelection"
                                        @editData="editData"
                                        @confirmDeletion="confirmDeletion"
                                        @viewData="viewData"
                                    />
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="14" class="text-center">
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Asset</span>
                        <span v-else>Add Asset Category</span>
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
                    :validation-schema="
                        editing ? editUserSchema : createUserSchema
                    "
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
                                        <label for="user">Category Name</label>
                                        <Field
                                            name="name"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.name,
                                            }"
                                            id="name"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Category Name"
                                            v-model="form.name"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.name
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user">Category Code</label>
                                        <Field
                                            name="categorycode"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.categorycode,
                                            }"
                                            id="categorycode"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Category Code"
                                            v-model="form.categorycode"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.categorycode
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user">Description</label>
                                        <Field
                                            name="description"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.description,
                                            }"
                                            id="description"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Description"
                                            v-model="form.description"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.description
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <FormCheckRadioGroup
                                        v-model="form.status"
                                        name="status"
                                        :options="{ status: 'Active' }"
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
                        @click.prevent="deleteData"
                        type="button"
                        class="btn btn-primary"
                    >
                        Delete Record
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>
