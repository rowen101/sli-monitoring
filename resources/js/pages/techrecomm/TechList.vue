<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import UserListItem from "./TechListItem.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import ContentLoader from "../../components/ContentLoader.vue";
import { useRoute } from "vue-router";
const statusid = ref();
const listsite = ref();
const toastr = useToastr();
const lists = ref({ data: [] });
const tecstatus = ref([
    {
        name: "PENDING",
        value: 1,
    },
    {
        name: "APPROVED",
        value: 2,
    },
    {
        name: "CANCELLED",
        value: 3,
    },
]);
const pageTitle = `${useRoute().name}`;
const getSite = () => {
    axios
        .get(`/api/getsite`)
        .then((response) => {
            listsite.value = response.data.sites;
        })
        .catch((error) => {
            console.log(error);
        });
};
const isloading = ref(false);
const editing = ref(false);
const formValues = ref();

const form = reactive({
    id: '',
    recommnum: '',
    branch: '',
    department: '',
    warehouse: '',
    user: '',
    problem: '',
    brand: '',
    model: '',
    assettag: '',
    serialnum: '',
    assconducted: '',
    recommendation: '',
    ceated_by: '',
});
const authUserStore = useAuthUserStore();
const selectedStatus = ref(null);
const getItems = (page = 1) => {
    isloading.value = true;
    axios
        .get(`/api/tech-recommendations?page=${page}`, {
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
    branch: yup.string().required(),
    department: yup.string().required(),
    user: yup.string().required(),
    problem: yup.string().required(),
    assconducted: yup.string().required(),
    brand: yup.string().required(),
});

const editUserSchema = yup.object({
    branch: yup.string().required(),
    department: yup.string().required(),
    user: yup.string().required(),
    problem: yup.string().required(),
    assconducted: yup.string().required(),
    brand: yup.string().required(),
});

const createData = (values, { resetForm, setErrors }) => {
    axios
        .post("/api/tech-recommendations", values)
        .then((response) => {
            lists.value.data.unshift(response.data);
            $("#FormModal").modal("hide");
            resetForm();
            toastr.success("Data created successfully!");
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
const resetForm = () => {
    form.id = "";
    form.recommnum = "";
    form.branch = "";
    form.department = "";
    form.warehouse = "";
    form.user = "";
    form.problem = "";
    form.brand = "";
    form.model = "";
    form.assettag = "";
    form.serialnum = "";
    form.assconducted = "";
    form.recommendation = "";
    form.ceated_by = "";
}
const editData = ( item ) => {

    statusid.value = item.id;
    editing.value = true;

       resetForm();
        form.id = item.id;
        form.branch= item.branch;
        form.department=item.department;
        form.warehouse= item.warehouse;
        form.user= item.user;
        form.problem= item.problem;
        form.brand= item.brand;
        form.model= item.model;
        form.assettag= item.assettag;
        form.serialnum= item.serialnum;
        form.assconducted= item.assconducted;
        form.recommendation= item.recommendation;
        form.ceated_by= item.ceated_by;
        form.recommnum = item.recommnum;

 $("#FormModal").modal("show");

};



const updateData = (form, { setErrors }) => {
    axios
        .put("/api/tech-recommendations/" + form.id, form)
        .then((response) => {
            // const index = lists.value.data.findIndex(
            //     (item) => item.id === response.data.id
            // );
            // lists.value.data[index] = response.data;
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
        updateData(form, actions);
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
        .delete(`/api/tech-recommendations/${userIdBeingDeleted.value}`)
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
        .delete("/api/tech-recommendations", {
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

const handleGetAction = (statusItem) => {
    axios
        .post("/api/tech-getaction", {
            id: statusid.value,
            status: statusItem,


        })
        .then((response) => {
            $("#FormModal").modal("hide");
            getItems();
        })
        .catch((error) => {
            console.log(error.message);
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
                        TechRecom
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
                  
                        <ContentLoader v-if="isloading"/>
                    <div class="dispatch-table">
                        <div class="table-responsive">
                            <font size="2" >
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <!-- <th>
                                    <input
                                        type="checkbox"
                                        v-model="selectAll"
                                        @change="selectAllUsers"
                                    />
                                </th> -->

                                        <th>Technom</th>
                                        <th>User</th>
                                        <th>Branch</th>
                                        <th>Department</th>
                                        <th>Created by</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="lists.data.length > 0">
                                    <UserListItem
                                        v-for="(item, index) in lists.data"
                                        :key="item.id"
                                        :item="item"
                                        :index="index"
                                        @edit-user="editData"
                                        @show-item="viewItem"
                                        @confirm-user-deletion="
                                            confirmItemDeletion
                                        "
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
                    <div
                        class="dispatch-list"
                        v-for="(item, index) in lists.data"
                        :key="item.id"
                        :item="item"
                        :index="index"
                    >
                        <div class="card">
                            <div class="card-body">
                                <div class="list-field">
                                    <span class="mb-1 dis">Tech #:</span>
                                    <span
                                        ><b>{{ item.recommnum }}</b></span
                                    >
                                </div>
                                <div class="list-field">
                                    <span class="mb-1 dis">User:</span>
                                    <span>{{ item.user }}</span>
                                </div>
                                <div class="list-field">
                                    <span class="mb-1 dis">Branch:</span>
                                    <span>{{ item.branch }}</span>
                                </div>
                                <div class="list-field">
                                    <span class="mb-1 dis">Department:</span>
                                    <span>{{ item.department }}</span>
                                </div>
                                <div class="list-field">
                                    <span class="mb-1 dis">Created by:</span>
                                    <span>{{ item.created_user }}</span>
                                </div>
                                <div class="list-field">
                                    <span class="mb-1 dis">Status:</span>
                                    <router-link v-if="item.statusid ==2"
                :to="`/admin/tech-approved/${item.recommnum}/view`"
                target="_blank"
            >
                <span class="badge" :class="`badge-${item.status.color}`">
                    {{ item.status.name }}
                </span>
            </router-link>

            <span v-if="item.statusid !=2" class="badge" :class="`badge-${item.status.color}`">
                {{ item.status.name }}
            </span>
                                </div>
                                <div class="list-field">
                                    <span class="mb-1 dis">Created Date:</span>
                                    <span>{{ item.created_at }}</span>
                                </div>
                                <hr />
                                <div class="row">
                                    <div
                                        class="col"
                                        v-if="
                                            authUserStore.user.role === 'ADMIN'
                                        "
                                    >
                                        <button
                                            variant="primary"
                                            class="btn btnsm btn-primary btn-block"
                                            @click="editData(item)"
                                        >
                                            <i
                                                class="fa fa-pencil-square-o"
                                            ></i>
                                            Edit
                                        </button>
                                        <button
                                            class="btn btnsm btn-danger btn-block"
                                            @click="
                                                confirmItemDeletion(item.id)
                                            "
                                        >
                                            <span class="text-light">
                                                <i class="fa fa-trash-o"></i>
                                                Remove
                                            </span>
                                        </button>

                                         <button
                                           v-if="item.statusid == 2"
                                            class="btn btnsm btn-success btn-block"

                                        >
                                            <i
                                                class="fa fa-pencil-square-o"
                                            ></i>
                                            Download

                                        </button>

                                    </div>

                                    <div
                                        class="col"
                                        v-if="
                                            authUserStore.user.role === 'USER'
                                        "
                                    >
                                    <div v-if="item.statusid !=2">
                                         <button
                                         v-if="item.created_by == authUserStore.user.id"
                                            variant="primary"
                                            class="btn btnsm btn-primary btn-block"
                                            @click="editData(item)"
                                        >
                                            <i
                                                class="fa fa-pencil-square-o"
                                            ></i>
                                            Edit
                                        </button>
                                    </div>



                                    </div>


                                </div>
                            </div>
                        </div>
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
                        <span v-if="editing">Edit Tech Recom.</span>
                        <span v-else>Add New Tech Recom.</span>
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
                                <div class="col-6">
                                    <Field
                                        type="hidden"
                                        name="created_by"
                                        id="created_by"
                                        v-model="authUserStore.user.id"
                                    />

<Field
                                        type="hidden"
                                        name="recommnum"
                                        id="recommnum"
                                        v-model="form.recommnum"
                                    />
                                    <div class="form-group">
                                        <label for="branch">Branch</label>
                                        <Field
                                            as="select"
                                            name="branch"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.branch,
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
                                        <span class="invalid-feedback">{{
                                            errors.branch
                                        }}</span>
                                    </div>



                                    <div class="form-group">
                                        <label for="department"
                                            >Department</label
                                        >
                                        <Field
                                            name="department"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.department,
                                            }"
                                            id="department"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Department"
                                            v-model="form.department"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.department
                                        }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="warehouse">warehouse</label>
                                        <Field
                                            name="warehouse"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.warehouse,
                                            }"
                                            id="email"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Warehouse"
                                            v-model="form.warehouse"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.warehouse
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="user">User</label>
                                        <Field
                                            name="user"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.user,
                                            }"
                                            id="user"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter User"
                                            v-model="form.user"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.user
                                        }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="brand">Brand</label>
                                        <Field
                                            name="brand"
                                            type="text"
                                            class="form-control"
                                            id="brand"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter brand"
                                            v-model="form.brand"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label for="model">Model</label>
                                        <Field
                                            name="model"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.model,
                                            }"
                                            id="model"
                                            v-model="form.model"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Model"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.model
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="assettag">Asset tag</label>
                                        <Field
                                            name="assettag"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.assettag,
                                            }"
                                            id="assettag"
                                            v-model="form.assettag"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Asset Tag"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.assettag
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="serialnum"
                                            >Serial number</label
                                        >
                                        <input
                                            name="serialnum"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.serialnum,
                                            }"
                                            id="serialnum"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Serial Number"
                                            v-model="form.serialnum"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.serialnum
                                        }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="problem"
                                            >Report Problem</label
                                        >
                                        <Field
                                            name="problem"
                                            as="textarea"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.problem,
                                            }"
                                            id="problem"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Report problem"
                                            v-model="form.problem"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.problem
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="assconducted"
                                            >Assessment conducted</label
                                        >
                                        <Field
                                            name="assconducted"
                                            as="textarea"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.assconducted,
                                            }"
                                            id="assconducted"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Assessment conducted"
                                            v-model="form.assconducted"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.assconducted
                                        }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="recommendation"
                                            >Recommendation</label
                                        >
                                        <Field
                                            name="recommendation"
                                            as="textarea"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.recommendation,
                                            }"
                                            id="recommendation"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Recommendation"
                                            v-model="form.recommendation"
                                        />
                                        <span class="invalid-feedback">{{
                                            errors.recommendation
                                        }}</span>
                                    </div>
                                </div>
                            </div>
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
