<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch, inject } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import JobItemList from "./JobItemList.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore.js";
import ContentLoader from "../../components/ContentLoader.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import { useRoute } from "vue-router";
const swal = inject("$swal");
const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const lists = ref({ data: [] });
const menuOptionlist = ref({ data: [] });

const editing = ref(false);
const formValues = ref();
const isLoading = ref(false);
const authUserStore = useAuthUserStore();
const selectedStatus = ref(null);
const selectedParentID = ref();

const getItems = (page = 1) => {
    isLoading.value = true;
    axios
        .get(`/web/job-request?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            isLoading.value = false;
            lists.value = response.data;
            selectedItems.value = [];
            selectAll.value = false;
        })
        .finally(() => {
            isLoading.value = false;
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

const selectAll = ref(false);
const viewItem = ref(false);
const confirmItemDeletion = async (item) => {
    const result = await swal.fire({
        title: "Are you sure you want to Reject this record?",
        icon: "warning",
        showCancelButton: true,
    });
    if (result.isConfirmed) {
        isLoading.value = true;
        axios
            .put(`/web/job-request/${item.id}`, { status: "C" })
            .then((response) => {
                toastr.success('Record successfully Rejected');
                getItems();
            })
            .catch((error) => {
                toastr.error(error.response.data.errors);
            })
            .finally(() => {
                isLoading.value = false;
            });
    }
};

const editData = async (item) => {
    const result = await swal.fire({
        title: "Are you sure you want to Approve this record?",
        icon: "warning",
        showCancelButton: true,
    });
    if (result.isConfirmed) {
        isLoading.value = true;
        axios
            .put(`/web/job-request/${item.id}`, { status: "A" })
            .then((response) => {
                toastr.success('Record successfully Approved');
                getItems();
            })
            .catch((error) => {
                toastr.error(error.data.message);
            });
    }
};

const deleteData = () => {};
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
     <div v-if="isLoading" class="spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>
    <div class="content">

        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link
                        :class="'mb-2 btn btn-primary'"
                        :to="{ name: 'Job Order Create' }"
                    >
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
                            >Selected {{ selectedItems.length }} Menu</span
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
                    <ContentLoader v-if="isLoading" />
                    <div class="table-responsive">
                        <font size="2">
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
                                        <th style="width: 10px">#</th>
                                        <th>Site</th>
                                        <th>Order Number</th>
                                        <th>Requested Date</th>
                                        <th>Needed Date</th>
                                        <th>Status</th>
                                        <th>Createdby</th>
                                        <th>CreatedAt</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="lists.data.length > 0">
                                    <JobItemList
                                        v-for="(item, index) in lists.data"
                                        :key="item.id"
                                        :item="item"
                                        :index="index"
                                        @approvedata="editData"
                                        @show-item="viewItem"
                                        @confirmDataDeletion="
                                            confirmItemDeletion
                                        "
                                        @toggle-selection="toggleSelection"
                                        :select-all="selectAll"
                                    />
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="11" class="text-center">
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
        id="deleteRecordModal"
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
                        <span>Reject Record</span>
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
                    <h5>Are you sure you want to Reject this record ?</h5>
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
                        Reject Record
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.spinner {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1050; /* Make sure it's above other content */
    display: flex;
    align-items: center;
    justify-content: center;
}
.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
