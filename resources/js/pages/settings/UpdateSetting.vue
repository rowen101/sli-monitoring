<script setup>
import { ref, onMounted, reactive, watch, computed } from "vue";
import { useToastr } from "@/toastr";
import ContentLoader from "../../components/ContentLoader.vue";
import { Form, Field, useResetForm } from "vee-validate";
import { useRoute } from "vue-router";

const pageTitle = `${useRoute().name}`;
const settings = ref([]);
const toastr = useToastr();
const showList = ref(true);
const listItem = ref([]);
const isLoadingSite = ref(false);
const formValues = ref();
//site
const formSite = ref({
    id: "",
    site_name: "",
    is_active: "",
});

const getItems = () => {
    isLoadingSite.value = true;
    axios
        .get(`/api/site`)
        .then((response) => {
            isLoadingSite.value = false;
            listItem.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const removeSite = () => {
    isLoadingSite.value = true;
    axios
        .delete(`/api/site/`  )
        .then((response) => {
            isLoadingSite.value = false;
            listItem.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const deActivatedTask = computed(() => {
    if (!Array.isArray(listItem.value)) {
        return [];
    }

    return listItem.value.filter((item) => item.is_active === 0);
});

const deactivelist = computed(() => deActivatedTask.value.length);
const toggleList = () => {
    showList.value = !showList.value;
};
const handleSubmit = () => {
    // Assuming isLoadingSite and errors are defined in your setup
    isLoadingSite.value = true;
    axios
        .post("/api/site", {
            id: formSite.value?.id,
            site_name: formSite.value?.site_name,
            is_active: true,
        })
        .then((response) => {
            // Assuming getItems is a function to fetch data
            getItems();
            // Reset the form after successful post
            formSite.value = { id: null, site_name: "", is_active: true };
            toastr.success(response.data.message);
            isLoadingSite.value = false;
        })
        .catch((error) => {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
        });
};

// const createData = (values, { resetForm, setErrors }) => {
//    axios
//         .post('/api/site', formSite)
//         .then((response) => {
//           // Assuming getItems is a function to fetch data
//           getItems();
//           toastr.success(response.data.message);
//           isLoadingSite.value = false;
//         })
//         .catch((error) => {
//           if (error.response && error.response.status === 422) {
//             errors.value = error.response.data.errors;
//           }
//         });
// };

// click task item to complete or cancel
const handleIsActiveSite = (item) => {

    formSite.value.is_active = item.is_active === 1 ? 0 : 1;

    axios
        .post("/api/site", {
            id: item.id,
            site_name: item.site_name,
            is_active: formSite.value.is_active,
        })
        .then((response) => {
            // Fetch tasks using GET request after the POST request is successful
            axios
                .get(`/api/site`)
                .then((response) => {
                    listItem.value = response.data;
                    //toastr.success("Data created successfully!");
                })
                .catch((error) => {
                    console.error("Error fetching tasks:", error);
                    toastr.error("Error fetching tasks. Please try again.");
                });
        })
        .catch((error) => {
            console.error("Error adding new task:", error);
            toastr.error("Error adding new task. Please try again.");
        });
};

const editData = (item) => {

    formSite.value.id = item.id;
    formSite.value.site_name = item.site_name;
}
const getSettings = () => {
    axios.get("/api/settings").then((response) => {
        settings.value = response.data;
    });
};

const errors = ref();
const updateSettings = () => {
    errors.value = "";
    axios
        .post("/api/settings", settings.value)
        .then((response) => {
            toastr.success("Settings updated successfully!");
        })
        .catch((error) => {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
        });
};

onMounted(() => {
    getItems();
    getSettings();
    document.title = pageTitle;

});
</script>

<template>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <ul class="nav nav-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a
                                    class="nav-link active"
                                    id="general-tab"
                                    data-toggle="tab"
                                    href="#general"
                                    >General Settings</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    id="site-tab"
                                    data-toggle="tab"
                                    href="#site"
                                    >Site Settings</a
                                >
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- General Settings Tab -->
                            <div class="tab-pane fade show active" id="general-tab">
                                <form @submit.prevent="updateSettings()">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="appName"
                                                >App Display Name</label
                                            >
                                            <input
                                                v-model="settings.app_name"
                                                type="text"
                                                class="form-control"
                                                id="appName"
                                                placeholder="Enter app display name"
                                            />
                                            <span
                                                class="text-danger text-sm"
                                                v-if="errors && errors.app_name"
                                                >{{ errors.app_name[0] }}</span
                                            >
                                        </div>
                                        <div class="form-group">
                                            <label for="dateFormat"
                                                >Date Format</label
                                            >
                                            <select
                                                v-model="settings.date_format"
                                                class="form-control"
                                            >
                                                <option value="m/d/Y">
                                                    MM/DD/YYYY
                                                </option>
                                                <option value="d/m/Y">
                                                    DD/MM/YYYY
                                                </option>
                                                <option value="Y-m-d">
                                                    YYYY-MM-DD
                                                </option>
                                                <option value="F j, Y">
                                                    Month DD, YYYY
                                                </option>
                                                <option value="j F Y">
                                                    DD Month YYYY
                                                </option>
                                            </select>
                                            <span
                                                class="text-danger text-sm"
                                                v-if="
                                                    errors && errors.date_format
                                                "
                                                >{{
                                                    errors.date_format[0]
                                                }}</span
                                            >
                                        </div>
                                        <div class="form-group">
                                            <label for="paginationLimit"
                                                >Pagination Limit</label
                                            >
                                            <input
                                                v-model="
                                                    settings.pagination_limit
                                                "
                                                type="text"
                                                class="form-control"
                                                id="paginationLimit"
                                                placeholder="Enter pagination limit"
                                            />
                                            <span
                                                class="text-danger text-sm"
                                                v-if="
                                                    errors &&
                                                    errors.pagination_limit
                                                "
                                                >{{
                                                    errors.pagination_limit[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >
                                            <i class="fa fa-save mr-1"></i>Save
                                            Changes
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Site Settings Tab -->
                            <div class="tab-pane fade" id="site">
                                <div
                                    class="mt-2"
                                    v-if="listItem.length > 0"
                                    style="max-height: 300px; overflow-y: auto"
                                >
                                    <!-- Separate List for incomplete tasks -->
                                    <ContentLoader v-if="isLoadingSite"/>

                                    <ul
                                        class="list-group"
                                        v-for="item in listItem"
                                        :key="item.id"
                                    >
                                        <li
                                            v-if="item.is_active === 1"
                                            class="list-group-item mt-2"
                                        >
                                            <div
                                                class="d-flex justify-content-between"
                                            >
                                                <div class="d-flex">
                                                    <i
                                                        @click="
                                                            handleIsActiveSite(
                                                                item
                                                            )
                                                        "
                                                        :class="{
                                                            'cursor-pointer mr-2': true,
                                                            'fa fa-check-circle text-primary':
                                                                item.is_active ===
                                                                1,
                                                            'fa fa-circle text-primary':
                                                                item.is_active ===
                                                                0,
                                                        }"
                                                        style="font-size: 15px"
                                                    ></i>

                                                    <span
                                                        :class="{
                                                            'font-italic text-red':
                                                                item.is_active ===
                                                                0,
                                                            '':
                                                                item.is_active ===
                                                                1,
                                                        }"
                                                    >
                                                        {{ item.site_name }}

                                                    </span>
                                                </div>
                                                <div class="d-flex">
                                                    <i
                                                        class="fa fa-pen text-success"
                                                        @click="editData(item)"
                                                    ></i>


                                                </div>
                                            </div>
                                        </li>
                                    </ul>

                                    <!-- List for completed tasks -->
                                    <li class="m-2 list-unstyled">
                                        <button
                                            class="btn btn-sm bg-danger"
                                            @click="toggleList"
                                            v-if="deactivelist > 0"
                                        >
                                            <i
                                                :class="[
                                                    'fa',
                                                    showList
                                                        ? 'fa-arrow-down'
                                                        : 'fa-arrow-right',
                                                ]"
                                            ></i>
                                            &nbsp;Deactive List
                                            {{ deactivelist }}
                                        </button>
                                    </li>
                                    <div v-if="showList">
                                        <ul
                                            class="list-group"
                                            v-for="item in listItem"
                                            :key="item.id"
                                        >
                                            <li
                                                v-if="item.is_active === 0"
                                                class="list-group-item mt-2"
                                            >
                                                <div
                                                    class="d-flex justify-content-between"
                                                >
                                                    <div class="d-flex">
                                                        <i
                                                            @click="
                                                                handleIsActiveSite(
                                                                    item
                                                                )
                                                            "
                                                            :class="{
                                                                'cursor-pointer mr-2': true,
                                                                'fa fa-check-circle text-primary':
                                                                    item.is_active ===
                                                                    1,
                                                                'fa fa-circle text-primary':
                                                                    item.is_active ===
                                                                    0,
                                                            }"
                                                            style="
                                                                font-size: 15px;
                                                            "
                                                        ></i>

                                                        <span
                                                            :class="{
                                                                'font-italic text-red':
                                                                    item.is_active ===
                                                                    0,
                                                                '':
                                                                    item.is_active ===
                                                                    1,
                                                            }"
                                                        >
                                                            <del>{{
                                                                item.site_name
                                                            }}</del>
                                                        </span>
                                                    </div>
                                                    <!-- <div class="d-flex">
                                                        <i
                                                            v-if="
                                                                item.is_active ===
                                                                1
                                                            "
                                                            class="fa fa-pen text-success mr-2"
                                                            @click="editData(item)"
                                                        ></i>
                                                    </div> -->

                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-2 text-center" v-else>
                                    <span>No Site</span>
                                </div>
                                <Form
                                    ref="form"
                                    @submit="handleSubmit"
                                    :initial-values="formValues"
                                >
                                    <Field
                                        name="id"
                                        v-model="formSite.id"
                                        type="text"
                                        hidden
                                    />
                                    <div class="input-group">
                                        <Field
                                            name="site_name"
                                            type="text"
                                            class="form-control"
                                            id="site_name"
                                            v-model="formSite.site_name"
                                            aria-describedby="nameHelp"
                                            placeholder="Enter Site Name"
                                        />
                                        <div class="input-group-append">
                                            <button
                                                type="submit"
                                                class="btn btn-primary btn-sm"
                                            >
                                                <i class="fas fa-warehouse"></i>
                                            </button>
                                        </div>
                                    </div>
                                </Form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
a {
    color: #2b2b2b;
    text-decoration: none;
}
.nav-tabs {
    border-bottom: 2px solid #2196f3;
}

.nav-tabs .nav-item {
    margin-bottom: -1px;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-radius: 0;
    color: #2196f3;
    transition: background-color 0.3s;
}

.nav-tabs .nav-link.active {
    background-color: #2196f3;
    color: #fff;
    border-color: #2196f3;
}

.nav-tabs .nav-link:hover {
    background-color: #0069d9;
    border-color: #0069d9;
}
</style>
