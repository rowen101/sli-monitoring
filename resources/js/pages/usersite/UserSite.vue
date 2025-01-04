<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import { useRoute } from "vue-router";

const authUserStore = useAuthUserStore();
const pageTitle = `${useRoute().name}`;
const toastr = useToastr();

const checked = ref(true);
const form = reactive({
    user_id: "",
});

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

const loadUserSites = (userId) => {
    axios
        .get(`/web/usersite/${userId}`)
        .then((response) => {
            listsite.value = response.data;
            selectedsite.value = listsite.value
                .filter((site) => site.hasAccess === 1)
                .map((site) => site.id);

        })
        .catch((error) => {
            console.error("Error fetching site data for user:", error);
        });
};

const handleSubmit = () => {
    // Prepare the data to be sent to your API
    const postData = {
        user_id: form.user_id,
        site_id: selectedsite.value,
        // other data properties
    };
    console.log(postData);
    // Make the API call using your preferred method (Axios, fetch, etc.)
    // Example using Axios:
    axios
        .post("/web/onSaveupdate", postData)
        .then((response) => {
            toastr.success("User Site Save successfully!");
        })
        .catch((error) => {
            setErrors(error.response.data.errors);
        });
};

const changeSite = (val) => {
    loadUserSites(val);
};
onMounted(() => {
    getUser();
    loadUserSites();
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
                                    name="user_id"
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

                        <div class="card-body" v-if="form.user_id">
                            <ul
                                class="list-group"
                                style="max-height: 300px; overflow-y: auto"
                            >

                                <li
                                    v-for="site in listsite"
                                    :key="site.id"
                                    class="list-group-item"
                                >
                                    <div class="custom-control custom-checkbox">
                                        <input
                                            class="custom-control-input"
                                            type="checkbox"
                                            v-model="selectedsite"
                                            :value="site.id"
                                            :id="'site_id_' + site.id"

                                        />
                                        <label
                                            :for="'site_id_' + site.id"
                                            class="custom-control-label"
                                        >
                                            {{ site.site_name }}
                                        </label>
                                    </div>
                                </li>
                            </ul>

                            <div class="card-footer">
                                <button
                                    type="button"
                                    @click="handleSubmit()"
                                    class="btn btn-primary"
                                >
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
