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
        .get(`/web/asset-monitoring?page=${page}`, {
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

const parentMenus = () => {
    axios
        .get(`/api/GetParentId`)
        .then((response) => {
            menuOptionlist.value = response.data;
        })
        .catch((error) => {
            console.error(error);
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
};
const createData = ({ resetForm, setErrors }) => {
    axios
        .post("/api/menulist", {
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

const updateData = ({ setErrors }) => {
    axios
        .post("/api/menulist", form)
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

const onComplete = () => {
    alert("message");
};
const deleteUser = () => {
    axios.delete(`/api/menu/${valIdBeingDeleted.value}`).then(() => {
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
    parentMenus();
    document.title = pageTitle;
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
                        Asset
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
                                        <th>Asset</th>
                                        <th>Asset type</th>
                                        <th>Serial</th>
                                        <th>Date Aquired</th>
                                        <th>Suppliear</th>
                                        <th>Unit</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody v-if="lists.data.length > 0">
                                    <AssetmItemList
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Asset</span>
                        <span v-else>Add Asset</span>
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
                            <form-wizard
                                @on-complete="onComplete"
                                color="#094899"
                                step-size="xs"
                            >
                                <tab-content
                                    title="Asset Information"
                                    icon="fas fa-book"
                                >

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="branch"
                                                    >Branch</label
                                                >
                                                <Field
                                                    as="select"
                                                    name="branch"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.branch,
                                                    }"
                                                    id="branch"
                                                    aria-describedby="branchHelp"
                                                    v-model="form.branch"
                                                >
                                                    <option value="" disabled>
                                                        Select Branch
                                                    </option>
                                                    <!-- Default empty option -->
                                                    <!-- Populate options from listsite -->
                                                    <option
                                                        v-for="site in listsite"
                                                        :key="site.site_name"
                                                        :value="site.site_name"
                                                    >
                                                        {{ site.site_name }}
                                                    </option>
                                                </Field>
                                                <span
                                                    class="invalid-feedback"
                                                    >{{ errors.branch }}</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="department"
                                                    >Asset Name</label
                                                >
                                                <Field
                                                    name="asset_name"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.asset_name,
                                                    }"
                                                    id="asset_name"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter asset_name"
                                                    v-model="form.asset_name"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.asset_name
                                                    }}</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="asset_type"
                                                    >Asset Type</label
                                                >
                                                <Field
                                                    name="asset_type"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.asset_type,
                                                    }"
                                                    id="asset_type"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter Asset Type"
                                                    v-model="form.asset_type"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.asset_type
                                                    }}</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="serial"
                                                    >Serial #:</label
                                                >
                                                <Field
                                                    name="serial"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.serial,
                                                    }"
                                                    id="serial"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter Serial no."
                                                    v-model="form.serial"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.serial
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                        <div class="col-6">



                                            <div class="form-group">
                                                <label for="date_acquired"
                                                    >Date Acquired</label
                                                >
                                                <Field
                                                    name="date_acquired"
                                                    type="date"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.date_acquired,
                                                    }"
                                                    id="date_acquired"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter Date Aquired"
                                                    v-model="form.date_acquired"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.date_acquired
                                                    }}</span
                                                >
                                            </div>

                                            <div class="form-group">
                                                <label for="man_supplier"
                                                    >Brand</label
                                                >

                                                <Field
                                                    name="man_supplier"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.man_supplier,
                                                    }"
                                                    id="man_supplier"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter Brand Name"
                                                    v-model="form.man_supplier"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.man_supplier
                                                    }}</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="unit">Unit</label>
                                                <Field
                                                    name="unit"
                                                    type="number"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.unit,
                                                    }"
                                                    id="unit"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter unit"
                                                    v-model="form.unit"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{ errors.unit }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </tab-content>

                                <tab-content title="Asset Allocation"  icon="fa fa-map">
                                    <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                                <label for="location">location</label>
                                                <Field
                                                    name="location"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.location,
                                                    }"
                                                    id="location"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter location"
                                                    v-model="form.location"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{ errors.location }}</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="paccountable">Person Accountable</label>
                                                <Field
                                                    name="paccountable"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.paccountable,
                                                    }"
                                                    id="paccountable"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter Person Accountable"
                                                    v-model="form.paccountable"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{ errors.paccountable }}</span
                                                >
                                            </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                                <label for="locationchangetranfer">Transfer Location</label>
                                                <Field
                                                    name="locationchangetranfer"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.locationchangetranfer,
                                                    }"
                                                    id="locationchangetranfer"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter tranfer Location"
                                                    v-model="form.locationchangetranfer"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{ errors.locationchangetranfer }}</span
                                                >
                                            </div>
                                    </div>

                                    </div>
                                </tab-content>
                                <tab-content title="Asset Condition">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="cucondition"
                                                    >Current Condition</label
                                                >
                                                <Field
                                                    as="select"
                                                    name="cucondition"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.cucondition,
                                                    }"
                                                    id="cucondition"
                                                    aria-describedby="branchHelp"
                                                    v-model="form.cucondition"
                                                >
                                                    <option value="" disabled>
                                                        Select Condition
                                                    </option>
                                                    <!-- Default empty option -->
                                                    <!-- Populate options from listsite -->
                                                    <option
                                                        v-for="site in listsite"
                                                        :key="site.site_name"
                                                        :value="site.site_name"
                                                    >
                                                        {{ site.site_name }}
                                                    </option>
                                                </Field>
                                                <span
                                                    class="invalid-feedback"
                                                    >{{ errors.cucondition }}</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="mentenancenotes">Maintenance Notes</label>
                                                <Field
                                                    name="mentenancenotes"
                                                    type="text"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.mentenancenotes,
                                                    }"
                                                    id="mentenancenotes"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter Maintenance Notes"
                                                    v-model="form.mentenancenotes"
                                                />

                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="lastmaintenance">Last Maintenance</label>
                                                <Field
                                                    name="lastmaintenance"
                                                    type="date"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.lastmaintenance,
                                                    }"
                                                    id="lastmaintenance"
                                                    aria-describedby="nameHelp"

                                                    v-model="form.lastmaintenance"
                                                />

                                            </div>
                                            <div class="form-group">
                                                <label for="nextmaintenance">Next Maintenance</label>
                                                <Field
                                                    name="nextmaintenance"
                                                    type="date"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.nextmaintenance,
                                                    }"
                                                    id="nextmaintenance"
                                                    aria-describedby="nameHelp"

                                                    v-model="form.nextmaintenance"
                                                />

                                            </div>
                                        </div>

                                    </div>
                                </tab-content>
                                <tab-content title="Utilization & Ussage">
                                    Utilization & Ussage
                                </tab-content>
                                <tab-content title="Financial Information" icon="fas fa-receipt">
                                    Financial Information
                                </tab-content>
                            </form-wizard>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div v-if="editing">
                            <div
                                v-if="authUserStore.user.role === 'ADMIN'"
                                class="btn-group float-l ml-auto"
                            >
                                <button
                                    type="button"
                                    @click="handleGetAction(2)"
                                    class="btn btn-info"
                                >
                                    Approved
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-info dropdown-toggle dropdown-hover dropdown-icon"
                                    data-toggle="dropdown"
                                >
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <span
                                        @click="handleGetAction(1)"
                                        class="dropdown-item"
                                        >Pending</span
                                    >
                                    <span
                                        @click="handleGetAction(3)"
                                        class="dropdown-item"
                                        >Disapproved</span
                                    >
                                </div>
                            </div>
                        </div>
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
                        Delete Record
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
