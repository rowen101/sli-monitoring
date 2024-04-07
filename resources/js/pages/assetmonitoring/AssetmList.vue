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
    site_name:"",
    id: "",
    site_id: "",
    asset_name: "",
    asset_type: "",
    serial: "",
    date_acquired: "",
    man_supplier: "",
    unit: "",
    location: "",
    paccountable: "",
    locationchangetranfer: "",
    cucodition: "",
    maintenancenotes: "",
});

const selectedStatus = ref(null);
const selectedParentID = ref();
const listsite = ref();

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
const createData = () => {
    axios
        .post("/web/asset-monitoring", form)
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
    form.site_name = item.site_name;
    form.id = item.id;
    form.site_id = item.site_id;
    form.asset_name = item.asset_name;
    form.asset_type = item.asset_type;
    form.serial = item.serial;
    form.date_acquired = item.date_acquired;
    form.man_supplier = item.man_supplier;
    form.unit = item.unit;
    form.location = item.location;
    form.paccountable = item.paccountable;
    form.locationchangetranfer = item.locationchangetranfer;
    form.cucodition = item.cucodition;
    form.maintenancenotes = item.maintenancenotes;
    form.ceated_by = item.ceated_by;
    form.updated_by = item.updated_by;
    $("#ViewRecodeModal").modal("show");
};
const editData = (item) => {
    editing.value = true;
    form.id = item.id;
    form.site_id = item.site_id;
    form.asset_name = item.asset_name;
    form.asset_type = item.asset_type;
    form.serial = item.serial;
    form.date_acquired = item.date_acquired;
    form.man_supplier = item.man_supplier;
    form.unit = item.unit;
    form.location = item.location;
    form.paccountable = item.paccountable;
    form.locationchangetranfer = item.locationchangetranfer;
    form.cucodition = item.cucodition;
    form.maintenancenotes = item.maintenancenotes;
    form.ceated_by = item.ceated_by;
    form.updated_by = item.updated_by;

    $("#FormModal").modal("show");

};

const updateData = ({ setErrors }) => {
    axios
        .post("/web/asset-monitoring", form)
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
const getSite = () => {
    axios
        .get(`/web/getsite`)
        .then((response) => {
            listsite.value = response.data.sites;
        })
        .catch((error) => {
            console.log(error);
        });
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
        .delete(`/web/asset-monitoring/${valIdBeingDeleted.value}`)
        .then((response) => {
            $("#deleteClientModal").modal("hide");
            toastr.success(response.data.message);
            getItems();
        });
};

const bulkDelete = () => {
    axios
        .delete("/web/bulkDeleteAsset", {
            data: {
                ids: selectedItems.value,
            },
        })
        .then((response) => {
            lists.value.data = lists.value.data.filter(
                (data) => !selectedItems.value.includes(data.id)
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
    getSite();

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
                                        <th>Site</th>
                                        <th>Asset</th>
                                        <th>Asset type</th>
                                        <th>Serial</th>
                                        <th>Date Aquired</th>
                                        <th>Supplier</th>
                                        <th>Unit</th>
                                        <th>Createdby</th>
                                        <th>CreatedAt</th>
                                        <th>Updatedby</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="lists.data.length > 0">
                                    <AssetmItemList
                                        v-for="(item, index) in lists.data"
                                        :key="item.id"
                                        :item="item"
                                        :index="index"
                                        @view-data="viewData"
                                        @edit-data="editData"
                                        @confirm-deletion="confirmDeletion"
                                        @toggle-selection="toggleSelection"
                                        :select-all="selectAll"
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
                                @on-complete="createData"
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
                                                <label for="branch">Site</label>
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
                                                    v-model="form.site_id"
                                                >
                                                    <option value="" disabled>
                                                        Select Site
                                                    </option>
                                                    <!-- Default empty option -->
                                                    <!-- Populate options from listsite -->
                                                    <option
                                                        v-for="site in listsite"
                                                        :key="site.id"
                                                        :value="site.id"
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
                                                    >{{ errors.serial }}</span
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

                                <tab-content
                                    title="Asset Allocation"
                                    icon="fa fa-map"
                                >
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="location"
                                                    >location</label
                                                >
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
                                                <label for="paccountable"
                                                    >Person Accountable</label
                                                >
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
                                                    >{{
                                                        errors.paccountable
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label
                                                    for="locationchangetranfer"
                                                    >Transfer Location</label
                                                >
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
                                                    v-model="
                                                        form.locationchangetranfer
                                                    "
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.locationchangetranfer
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </tab-content>
                                <tab-content title="Asset Condition">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="cucodition"
                                                    >Current Condition</label
                                                >
                                                <Field
                                                    as="select"
                                                    name="cucodition"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.cucodition,
                                                    }"
                                                    id="cucodition"
                                                    aria-describedby="branchHelp"
                                                    v-model="form.cucodition"
                                                >
                                                    <option value="" disabled>
                                                        Select Condition
                                                    </option>
                                                    <!-- Default empty option -->
                                                    <!-- Populate options from listsite -->
                                                    <option value="Fair">
                                                        FAIR
                                                    </option>
                                                    <option value="Good">
                                                        GOOD
                                                    </option>
                                                    <option value="Damange">
                                                        DAMANGE
                                                    </option>
                                                </Field>
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.cucodition
                                                    }}</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="mentenancenotes"
                                                    >Maintenance Notes</label
                                                >
                                                <Field
                                                    name="mentenancenotes"
                                                    as="textarea"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.mentenancenotes,
                                                    }"
                                                    id="mentenancenotes"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter Maintenance Notes"
                                                    v-model="
                                                        form.mentenancenotes
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="lastmaintenance"
                                                    >Last Maintenance</label
                                                >
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
                                                    v-model="
                                                        form.lastmaintenance
                                                    "
                                                />
                                            </div>
                                            <div class="form-group">
                                                <label for="nextmaintenance"
                                                    >Next Maintenance</label
                                                >
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
                                                    v-model="
                                                        form.nextmaintenance
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </tab-content>
                                <tab-content title="Utilization & Ussage">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="operationhours"
                                                    >Operating Hours</label
                                                >
                                                <Field
                                                    name="operationhours"
                                                    type="number"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.operationhours,
                                                    }"
                                                    id="operationhours"
                                                    aria-describedby="nameHelp"
                                                    v-model="
                                                        form.operationhours
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="notes">Notes</label>
                                                <Field
                                                    name="notes"
                                                    as="textarea"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.notes,
                                                    }"
                                                    id="notes"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter notes"
                                                    v-model="form.notes"
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{ errors.notes }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </tab-content>
                                <tab-content
                                    title="Financial Information"
                                    icon="fas fa-receipt"
                                >
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="purchasecost"
                                                    >Purchase Cost</label
                                                >
                                                <Field
                                                    name="purchasecost"
                                                    type="number"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.purchasecost,
                                                    }"
                                                    id="purchasecost"
                                                    aria-describedby="nameHelp"
                                                    v-model="form.purchasecost"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label
                                                    for="insurancewarrantyinfo"
                                                    >WARRANTY Information</label
                                                >
                                                <Field
                                                    name="insurancewarrantyinfo"
                                                    as="textarea"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.insurancewarrantyinfo,
                                                    }"
                                                    id="insurancewarrantyinfo"
                                                    aria-describedby="nameHelp"
                                                    placeholder="Enter insurancewarrantyinfo"
                                                    v-model="
                                                        form.insurancewarrantyinfo
                                                    "
                                                />
                                                <span
                                                    class="invalid-feedback"
                                                    >{{
                                                        errors.insurancewarrantyinfo
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </tab-content>
                            </form-wizard>
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
    <div
        class="modal fade"
        id="ViewRecodeModal"
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
                        <span>Asset Record</span>
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
    <div class="d-flex justify-content-between">
        <div class=" mb-3">

            <!-- info row -->
            <div class="row">
                <div class="col-md-12">
                    <p>
                        Site: {{ form.site_name }}<br>
                        Asset Name: {{ form.asset_name }}<br>
                        Asset Type: {{ form.asset_type }}<br>
                        Serial: {{ form.serial }}<br>
                        Date Acquired: {{ form.date_acquired }}<br>
                    </p>
                </div>
                <div class="col-md-12">
                    <p>
                        Brand: {{ form.man_supplier }}<br>
                        Unit: {{ form.unit }}<br>
                        Person Accountability: {{ form.paccountable }}<br>
                        Location Transfer: {{ form.locationchangetranfer }}<br>
                        Condition: {{ form.condition }}<br>
                        Maintenance Note: {{ form.maintenancenotes }}<br>
                    </p>
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
                </div>
            </div>
        </div>
    </div>
</template>
