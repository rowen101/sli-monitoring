<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch, inject } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import PalletItemList from "./PalletItemList.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import ContentLoader from "../../components/ContentLoader.vue";
import { useRoute } from "vue-router";
import Datepicker from "vue3-datepicker";
import html2canvas from "html2canvas";
const swal = inject("$swal");
import moment from "moment";
const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const lists = ref({ data: [] });
const listsite = ref();
const isloading = ref(false);
const editing = ref(false);
const authUserStore = useAuthUserStore();
const viewcost = ref(true);
const checked = ref(true);
const sitename = ref();
const form = reactive({
    id: "",
    site_id: "",
    user_id: authUserStore.user.id,
    date: "",
    allocatedpalletspace: "",
    spaceuteltotal: "",
    caseperpallet: "",
});

const selectedStatus = ref(null);

const totalcost = ref();

const getItems = () => {
    isloading.value = true;
    axios
        .get(`/web/pallet`, {
            params: {
                site_id: form.site_id,
                query: searchQuery.value,
            },
        })
        .then((response) => {
            isloading.value = false;
            lists.value = response.data.data;
            totalcost.value = response.data.sum;
            selectedItems.value = [];
            selectAll.value = false;
        });
};

const createSchema = yup.object({
    date: yup.string().required(),
    allocatedpalletspace: yup.string().required(),
    spaceuteltotal: yup.string().required(),
    caseperpallet: yup.string().required(),
});

const editSchema = yup.object({
    date: yup.string().required(),
    allocatedpalletspace: yup.string().required(),
    spaceuteltotal: yup.string().required(),
    caseperpallet: yup.string().required(),
});

const fromDate = ref("");
const toDate = ref("");

const formfilter = ref({
    start_date: "",
    end_date: "",
});

// Watch for changes in Sdate and StrHours and update plandate
watch([fromDate], () => {
    const originalDate = new Date(fromDate.value);
    const year = originalDate.getFullYear();
    const month = String(originalDate.getMonth() + 1).padStart(2, "0");
    const day = String(originalDate.getDate()).padStart(2, "0");
    formfilter.value.start_date = `${year}-${month}-${day}`;
});

// Watch for changes in Edate and EndHours and update planenddate
watch([toDate], () => {
    const originalDate = new Date(toDate.value);
    const year = originalDate.getFullYear();
    const month = String(originalDate.getMonth() + 1).padStart(2, "0");
    const day = String(originalDate.getDate()).padStart(2, "0");
    formfilter.value.end_date = `${year}-${month}-${day}`;
});

const applyFilter = () => {
    isloading.value = true;
    axios
        .get("/web/filter-pallet", {
            params: {
                start_date: formfilter.value.start_date,
                end_date: formfilter.value.end_date,
                site_id: form.site_id,
            },
        })
        .then((response) => {
            isloading.value = false;
            lists.value = response.data.data;
            totalcost.value = response.data.sum;
        })
        .catch((error) => {
            // Handle errors
            console.error(error);
        })
        .finally(() => {
            // Close the modal or perform any other actions
            $("#FormModalfilterDate").modal("hide");
        });
};

const ClearForm = () => {
    form.id = "";
    form.user_id = authUserStore.user.id;
    form.date = "";
    form.allocatedpalletspace = "";
    form.spaceuteltotal = "";
    form.caseperpallet = "";
};
const createData = ({ resetForm, setErrors }) => {
    axios
        .post("/web/pallet", {
            site_id: form.site_id,
            user_id: form.user_id,
            date: form.date,
            allocatedpalletspace: form.allocatedpalletspace,
            spaceuteltotal: form.spaceuteltotal,
            caseperpallet: form.caseperpallet,
        })
        .then((response) => {
            // lists.value.data.unshift(response.data);
            getItems();

            $("#FormModal").modal("hide");
            ClearForm();
            toastr.success("Data created successfully!");
        })
        .catch(function (error) {
            // Handle error
            if (error.response) {
                toastr.error(error.response.data.message);
            } else if (error.request) {
                toastr.error(error.request);
            } else {
                toastr.error("Error", error.message);
            }
        });
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

const changeSite = () => {

    getItems(form.site_id);

    const selectedSite = listsite.value.find(site => site.id === form.site_id);
    sitename.value = selectedSite ? selectedSite.site_name : '';

};

const addData = () => {
    // Check if listtasks is null
    if (!form.site_id || form.site_id.length === 0) {
        // Show a warning message using SweetAlert2
        swal.fire({
            title: "Warning!",
            text: "Select the Site First",
            icon: "warning",
        });
        return; // Exit the function early
    }
    editing.value = false;

    $("#FormModal").modal("show");
};

const editData = (item) => {
    editing.value = true;
    form.id = item.id;
    form.user_id = item.user_id;
    form.site_id = item.site_id;
    form.date = item.date;
    form.allocatedpalletspace = item.allocatedpalletspace;
    form.spaceuteltotal = item.spaceuteltotal;
    form.caseperpallet = item.caseperpallet;

    $("#FormModal").modal("show");
};

const updateData = ({ setErrors }) => {
    axios
        .post("/web/pallet", form)
        .then((response) => {
            ClearForm();
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

const toggleSelection = (data) => {
    const index = selectedItems.value.indexOf(data.id);
    if (index === -1) {
        selectedItems.value.push(data.id);
    } else {
        selectedItems.value.splice(index, 1);
    }
    console.log(selectedItems.value);
};

const valIdBeingDeleted = ref(null);

const confirmDeletion = (id) => {
    valIdBeingDeleted.value = id;
    $("#deleteDataModal").modal("show");
};

const deleteData = () => {
    axios.delete(`/web/pallet/${valIdBeingDeleted.value}`).then(() => {
        $("#deleteDataModal").modal("hide");
        toastr.success("Pallet deleted successfully!");
        lists.value.data = lists.value.data.filter(
            (val) => val.id !== valIdBeingDeleted.value
        );
    });
};

const bulkDelete = () => {
    axios
        .delete("/web/bulkDeletePallet", {
            data: {
                ids: selectedItems.value,
            },
        })
        .then((response) => {
            getItems();
            selectedItems.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};
const onFilterDate = () => {
    // Check if listtasks is null
    if (!form.site_id || form.site_id.length === 0) {
        // Show a warning message using SweetAlert2
        swal.fire({
            title: "Warning!",
            text: "Select the Site First",
            icon: "warning",
        });
        return; // Exit the function early
    }
    $("#FormModalfilterDate").modal("show");
};

const selectAll = ref(false);

const selectAllItems = () => {
    if (selectAll.value) {
        selectedItems.value = lists.value.data.map((user) => user.id);
    } else {
        selectedItems.value = [];
    }

};

const updateStatus = (status) => {
    selectedStatus.value = status;
};

//capture function
const onCapture = () => {

    $("#cost").hide();
    $("#capturecamerafilter").hide();
    const container = document.getElementById("captureDiv");

    html2canvas(container).then((canvas) => {
        const dataURL = canvas.toDataURL();
        const link = document.createElement("a");
        link.href = dataURL;
        link.download = `${sitename.value} - ${pageTitle}.png`;
        link.click();
    });
    $("#cost").show();
    $("#capturecamerafilter").show();
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
                        @click="addData"
                        type="button"
                        class=" mb-2 btn btn-primary"
                    >
                        <i class="fa fa-plus-circle mr-1"></i>
                        Pallet
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
                            >Selected {{ selectedItems.length }} Pallet</span
                        >
                    </div>
                </div>
                <div class="d-flex">
                    <div class="form-group">
                            <select
                                class="form-control"
                                id="siteDropdown"
                                v-model="form.site_id"
                                @change="changeSite()"
                            >
                                <option value="" disabled>Select a site</option>
                                <option
                                    v-for="site in listsite"
                                    :key="site.id"
                                    :value="site.id"

                                >
                                    {{ site.site_name }}
                                </option>
                            </select>
                        </div>
                </div>
                <!-- <div>
                    <input
                        type="text"
                        v-model="searchQuery"
                        class="form-control"
                        placeholder="Search..."
                    />
                </div> -->
            </div>
            <div id="captureDiv">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        {{ sitename || "Select a Site" }}
                    </div>

                    <div class="card-tools">
                        <div id="cost">
                        <div
                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                        >
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="customSwitch3"
                                v-model="viewcost"
                            />
                            <label
                                class="custom-control-label"
                                for="customSwitch3"
                                >Cost</label
                            >
                        </div>
                    </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="capturecamerafilter">
                    <div class="d-flex justify-content-between">

                            <div class="d-flex">
                                <button
                                    @click="onCapture"
                                    type="button"
                                    class="mb-2 btn btn-sm btn-success"
                                >
                                    <i class="fa fa-camera"></i>
                                </button>
                            </div>


                            <div class="d-flex">
                                <i
                                    class="fa fa-filter mr-1"
                                    @click="onFilterDate"
                                ></i>
                            </div>
                        </div>
                    </div>


                    <ContentLoader v-if="isloading"/>

                    <div v-else class="table-responsive">
                        <font size="2">
                            <table
                                class="table table-bordered table-sm table-striped table-hover"
                            >
                                <thead>
                                    <tr>
                                        <!-- <th>
                                            <input
                                                type="checkbox"
                                                v-model="selectAll"
                                                @change="selectAllItems"
                                            />
                                        </th> -->
                                        <th></th>

                                        <th>Date</th>
                                        <th>Allocated Pallet Space</th>
                                        <th>SPACE UTILIZATION TOTAL</th>
                                        <th>Space Utilization %</th>
                                        <th>Excess</th>
                                        <th v-if="viewcost">Cost per Pallet</th>
                                        <th v-if="viewcost">Cost</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="lists.length > 0">
                                    <tr
                                        v-for="(item, index) in lists"
                                        :key="item.id"
                                        :item="item"
                                        :index="index"
                                    >
                                        <td>
                                            <input
                                                type="checkbox"
                                                :checked="selectAll"
                                                @change="toggleSelection"
                                            />
                                        </td>

                                        <td>
                                            {{
                                                moment(item.date).format(
                                                    "MMMM DD, YY"
                                                )
                                            }}
                                        </td>

                                        <td>{{ item.allocatedpalletspace }}</td>
                                        <td>{{ item.spaceuteltotal }}</td>
                                        <td>
                                            {{ item.spacetotalutelpercent }}
                                        </td>
                                        <td>{{ item.excess }}</td>
                                        <td v-if="viewcost">
                                            {{ item.caseperpallet }}
                                        </td>
                                        <td v-if="viewcost">{{ item.cost }}</td>
                                        <td>
                                            EXCESS {{ item.excess }} PALLETS
                                        </td>

                                        <td>
                                            <a
                                                href="#"
                                                @click="editData(item)"
                                                ><i class="fa fa-edit"></i
                                            ></a>


                                        </td>
                                    </tr>

                                    <tr v-if="viewcost">
                                        <td
                                            colspan="7"
                                            style="text-align: right"
                                        >
                                            <b>Total:</b>
                                        </td>
                                        <td colspan="3">
                                            <b>{{ totalcost }}</b>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            No results found...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </font>
                    </div>
                </div>

            </div>
            <!-- <Bootstrap4Pagination
                :data="lists"
                @pagination-change-page="getItems"
            /> -->
        </div>
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
                        <span v-if="editing">Edit Pallet</span>
                        <span v-else>Add Pallet</span>
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
                    :validation-schema="editing ? editSchema : createSchema"

                >
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-12">
                                    <Field
                                        v-model="form.user_id"
                                        type="hidden"
                                        name="user_id"
                                        id="user_id"
                                    />

                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <Field
                                            name="date"
                                            type="date"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.date,
                                            }"
                                            id="date"
                                            aria-describedby="dateHelp"
                                            placeholder="Select Date"
                                            v-model="form.date"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.date
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user"
                                            >Allocated Pallet Space</label
                                        >
                                        <Field
                                            name="allocatedpalletspace"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.allocatedpalletspace,
                                            }"
                                            id="allocatedpalletspace"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Menu title"
                                            v-model="form.allocatedpalletspace"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.allocatedpalletspace
                                        }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="warehouse"
                                            >Space Utilization Total</label
                                        >
                                        <Field
                                            name="spaceuteltotal"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.spaceuteltotal,
                                            }"
                                            id="spaceuteltotal"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Menu Icon"
                                            v-model="form.spaceuteltotal"
                                            required
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.spaceuteltotal
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user"
                                            >Cost Per Pallet</label
                                        >
                                        <Field
                                            name="caseperpallet"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.caseperpallet,
                                            }"
                                            id="caseperpallet"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Case Per Pallet"
                                            v-model="form.caseperpallet"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.caseperpallet
                                        }}</span>
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
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="FormModalfilterDate"
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
                        <span>{{ pageTitle }} Report</span>
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
                    <div class="fromtocenter">
                        <div>
                            <label for="fromDate">From:</label>
                            <datepicker v-model="fromDate"></datepicker>
                        </div>

                        <div>
                            <label for="toDate">To:</label>
                            <datepicker v-model="toDate"></datepicker>
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
                    <button
                        @click="applyFilter()"
                        type="button"
                        class="btn btn-primary"
                    >
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.fromtocenter {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    /* Optional: Add additional styling if needed */
    margin-top: 5px; /* Adjust as needed */
}
</style>
