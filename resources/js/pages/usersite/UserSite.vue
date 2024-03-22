<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";

import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import { ContentLoader } from "vue-content-loader";
import { useRoute } from "vue-router";
import Datepicker from "vue3-datepicker";
const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const lists = ref({ data: [] });
const menuOptionlist = ref({ data: [] });

const isloading = ref(false);
const editing = ref(false);
const formValues = ref();

const checked = ref(true);
const form = reactive({
    user_id: "",
});

const authUserStore = useAuthUserStore();
const selectedStatus = ref(null);
const selectedParentID = ref();
const listsite = ref();
const listuser = ref();
const selectedsite = ref([]);
const getUser = () => {
    axios
        .get(`/api/users/userlist`)
        .then((response) => {
            listuser.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const getSite = (userId) => {
    // Renamed parameter for clarity

    if (userId !== null) {
        axios
            .get(`/api/usersite/${userId}`) // Updated endpoint with userId as a route parameter
            .then((response) => {
                listsite.value = response.data;
            })
            .catch((error) => {
                console.error("Error fetching site data for user:", error);
                // Handle error gracefully, e.g., show a message to the user
            });
    } else {
        axios
            .get("/api/getsitewithoutuser")
            .then((response) => {
                listsite.value = response.data;
            })
            .catch((error) => {
                console.error("Error fetching site data without user:", error);
                // Handle error gracefully, e.g., show a message to the user
            });
    }
};

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

const changeSite = (val) => {
    getSite(val);
};
onMounted(() => {
    getUser();
    getSite();
    document.title = pageTitle;
});
</script>

<template>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-title">
                            <div class="form-group m-3">
                                <Field
                                    as="select"
                                    name="branch"
                                    class="form-control"
                                    id="userid"
                                    v-model="form.user_id"
                                    @change="changeSite(form.user_id)"
                                >
                                    <option value="" disabled>
                                        Select User
                                    </option>

                                    <option
                                        v-for="data in listuser"
                                        :key="data.id"
                                        :value="data.id"
                                    >
                                        {{ data.name }}
                                    </option>
                                </Field>
                            </div>
                        </div>

                        <div class="card-body">
                            <fieldset class="border p-2">
                                <legend class="w-auto">Site</legend>
                                <ul
                                    class="list-group"
                                    style="max-height: 300px; overflow-y: auto"
                                    v-for="site in listsite"
                                    :key="site.site_name"
                                    :value="site.site_name"
                                >
                                    <li class="list-group-item">
                                        <input
                                            class="custom-control-input"
                                            type="checkbox"
                                            v-model="selectedsite"
                                            :value="site.id"
                                            :id="'site_id_' + site.id"
                                        />&nbsp;{{ site.site_name }}
                                    </li>
                                </ul>
                            </fieldset>

                            <div class="card-footer">
                                <button type="button" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
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
