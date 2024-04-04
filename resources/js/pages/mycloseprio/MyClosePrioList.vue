<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import MyClosePrioListItem from "./MyClosePrioListItem.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import html2canvas from "html2canvas";
import ContentLoader from "../../components/ContentLoader.vue";
import Datepicker from "vue3-datepicker";
import moment from 'moment';
import { useRoute } from "vue-router";

const pageTitle = `${useRoute().name}`;
const isloading = ref(false);
const toastr = useToastr();
const lists = ref({ data: [] });
const fromDate = ref("");
const toDate = ref("");
const authUserStore = useAuthUserStore();

const getItems = (page = 1) => {
    isloading.value = true;
    axios
        .get(`/api/mycloseprio?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            isloading.value = false;
            lists.value = response.data;
        });
};

const capturemycloseprio = () => {
    const container = document.getElementById("capturePrioContainer");

    html2canvas(container).then((canvas) => {
        const dataURL = canvas.toDataURL();
        const link = document.createElement("a");
        link.href = dataURL;
        link.download = "MY CLOSED PRIO.png";
        link.click();
    });
};
//filter datae
const onFilterDate = () => {
    $("#FormModalfilterDate").modal("show");
};
const applyFilter = () => {
    isloading.value = true;
    // Make an API request using Axios
    axios
        .get("/api/filter-closeprio", {
           params:{
             start_date: fromDate.value,
             end_date: toDate.value,
           }
        })
        .then((response) => {
            isloading.value = false;
            lists.value = response.data.dailyTasks;
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
const searchQuery = ref(null);

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
            <div class="card" id="capturePrioContainer">
                <div class="card-header">
                    <div class="card-title">
                        <h5>
                            {{
                                authUserStore.user.first_name +
                                " " +
                                authUserStore.user.last_name
                            }}
                            - My Closed Prio
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <ContentLoader v-if="isloading"/>
                    <div v-else class="content">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    <button
                                        @click="capturemycloseprio"
                                        type="button"
                                        class="mb-2 btn btn-sm btn-success"
                                    >
                                        <i class="fa fa-camera"></i>
                                    </button>
                                </div>
                                <!-- <div class="d-flex">
                                    <i
                                        @click="onFilterDate"
                                        class="fa fa-filter mr-1"
                                    ></i>
                                </div> -->
                            </div>

                            <div>
                                <div class="dispatch-table">
                                    <div class="table-responsive">
                                        <font size="2" >
                                        <table
                                            class="table table-bordered table-sm table-striped table-hover"
                                        >
                                            <thead>
                                                <tr>
                                                    <th>Site</th>
                                                    <th>Planned Date</th>

                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Task</th>

                                                    <th>Status</th>
                                                    <th>Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="lists.data.length > 0">
                                                <MyClosePrioListItem
                                                    v-for="(
                                                        item, index
                                                    ) in lists.data"
                                                    :key="item.id"
                                                    :item="item"
                                                    :index="index"
                                                />
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td
                                                        colspan="9"
                                                        class="text-center"
                                                    >
                                                        No results found...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </font>
                                    </div>
                                </div>

                                <div
                                    class="dispatch-list first-letter:shadow"
                                    v-for="(item, index) in lists.data"
                                    :key="item.id"
                                    :item="item"
                                    :index="index"
                                >
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="list-field">
                                                <span class="mb-1 dis"
                                                    >Site:</span
                                                >
                                                <span>{{
                                                   item.sitename
                                                }}</span>
                                            </div>
                                            <div class="list-field">
                                                <span class="mb-1 dis"
                                                    >Task Date:</span
                                                >
                                                <span>{{
                                                    moment(
                                                        item.taskdate
                                                    ).format("MMMM D, YYYY")
                                                }}</span>
                                            </div>
                                            <div class="list-field">
                                                <span class="mb-1 dis"
                                                    >Start Date:</span
                                                >
                                                <span>{{
                                                    item.startdate
                                                }}</span>
                                            </div>
                                            <div class="list-field">
                                                <span class="mb-1 dis"
                                                    >End Date:</span
                                                >
                                                <span>{{ item.enddate }}</span>
                                            </div>
                                            <div class="list-field">
                                                <span class="mb-1 dis"
                                                    >Task Type:</span
                                                >
                                                <span>{{
                                                    item.tasktype.listtask
                                                }}</span>
                                            </div>
                                            <div class="list-field">
                                                <span class="mb-1 dis"
                                                    >Status:</span
                                                >
                                                <span
                                                    :class="[
                                                        'badge',
                                                        item.remarks === 'HIT'
                                                            ? 'bg-success'
                                                            : 'bg-danger',
                                                    ]"
                                                >
                                                    {{ item.remarks }}</span
                                                >
                                            </div>
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
                        <span>My Close Prio</span>
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
                        @click="applyFilter"
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
