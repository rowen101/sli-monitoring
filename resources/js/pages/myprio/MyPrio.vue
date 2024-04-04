<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch, computed } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import MyListItem from "./MyPrioListItem.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import { useSettingStore } from "../../stores/SettingStore";
import flatpickr from "flatpickr";
import "flatpickr/dist/themes/light.css";
import moment from "moment";
import { inject } from "vue";
import ContentLoader from "../../components/ContentLoader.vue";

import Datepicker from "vue3-datepicker";
import { useRoute } from "vue-router";

const pageTitle = `${useRoute().name}`;
const isloading = ref(false);
const isloadingTask = ref(false);
const swal = inject("$swal");
const settingStore = useSettingStore();
const toastr = useToastr();
const lists = ref({ data: [] });
const listasks = ref([]);
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

const listsite = ref();
const listtask = ref();
const showList = ref(true);
const editing = ref(false);
const editingtask = ref(false);
const formValues = ref();
const currentdate = ref();

const taskdate = ref();
const startdate = ref();
const authUserStore = useAuthUserStore();
const selectedStatus = ref(null);
const showTaskList = ref(false);
const taskoptions = ref([
    {
        name: "LEAVE",
        value: 1,
    },
    {
        name: "ON BUSINESS TRAVEL",
        value: 2,
    },
    {
        name: "SITE VISIT",
        value: 3,
    },
    {
        name: "COA",
        value: 4,
    },
    {
        name: "WORK FROM HOME",
        value: 6,
    },
]);

const Sdate = ref();
const StrHours = ref("1:00 AM");

const Edate = ref();
const EndHours = ref("1:00 AM");

const starthours = ref(
    Array.from({ length: 24 }, (_, i) => {
        const hour = (i % 12) + 1;
        const period = i < 12 ? "AM" : "PM";
        return `${hour}:00 ${period}`;
    })
);

const endhours = ref(
    Array.from({ length: 24 }, (_, i) => {
        const hour = (i % 12) + 1;
        const period = i < 12 ? "AM" : "PM";
        return `${hour}:00 ${period}`;
    })
);

// Create a reactive form object
const form = reactive({
    dailytask_id:"",
    site: "",
    user_id: authUserStore.user.id,
    tasktype: 0,
    plandate: "",
    planenddate: "",
    enddates:"",
});

// Watch for changes in Sdate and StrHours and update plandate
watch([Sdate, StrHours], () => {
    // Create a new Date object from the original date string
    const originalDate = new Date(Sdate.value);

    // Extract the components of the date
    const year = originalDate.getFullYear();
    const month = String(originalDate.getMonth() + 1).padStart(2, "0");
    const day = String(originalDate.getDate()).padStart(2, "0");
    form.plandate = `${year}-${month}-${day} ${StrHours.value}`;
});

watch([Sdate, EndHours], () => {
    // Create a new Date object from the original date string
    const originalDate = new Date(Sdate.value);

    // Extract the components of the date
    const year = originalDate.getFullYear();
    const month = String(originalDate.getMonth() + 1).padStart(2, "0");
    const day = String(originalDate.getDate()).padStart(2, "0");
    //form.planenddate = `${year}-${month}-${day} ${EndHours.value}`;
});

// Watch for changes in Edate and EndHours and update planenddate
watch([Edate, EndHours], () => {
    const originalDate = new Date(Edate.value);
    // Extract the components of the date
    const year = originalDate.getFullYear();
    const month = String(originalDate.getMonth() + 1).padStart(2, "0");
    const day = String(originalDate.getDate()).padStart(2, "0");
    form.enddates = `${year}-${month}-${day} ${EndHours.value}`;
});

//task
const formtask = ref({
    id: "",
    dailytask_id: "",
    task_name: "",
    iscompleted: 0,
});

const formatDate = (dateString) => {
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
        timeZone: "Asia/Shanghai",
    };
    const formattedDate = new Date(dateString).toLocaleString("en-US", options);
    return formattedDate;
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
const getItems = () => {
    isloading.value = true;
    axios
        .get(`/api/dailytask`, {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            isloading.value = false;
            lists.value = response.data;
            selectedItems.value = [];
        });
};
//detroy task
const drop = async (id) => {
    // Show the SweetAlert2 dialog
    const result = await swal({
        title: "Are you sure?",
        text: "You wanna Drop this Todo?",
        icon: "warning",
        showCancelButton: true,
    });

    // Check if the user confirmed
    if (result.isConfirmed) {
        isloading.value = true;
        axios.put(`/api/dailytask/drop/${id}`).then((response) => {
            toastr.success("Data drop successfully!");
            isloading.value = false;
            getItems();
        });
    }
};
//show modal task
const OpenModalTask = () => {
    $("#FormModalTask").modal("show");
};

const showTasks = (value) => {
    OpenModalTask();
    formtask.value.dailytask_id = value.dailytask_id;
    taskdate.value = value.taskdate;
    startdate.value = value.startdate;
    isloadingTask.value = true;
    // Fetch tasks based on dailytask_id
    axios
        .get(`/api/dailytask/${value.dailytask_id}/tasks`)
        .then((response) => {
            listasks.value = response.data;
            OpenModalTask();
            isloadingTask.value = false;
        })
        .catch((error) => {
            console.error("Error fetching tasks:", error);
            // Handle the error appropriately, e.g., show an error message
        });
};

const AddNewTask = () => {
    isloadingTask.value = true;
    axios
        .post("/api/dailytask/addnewTask", {
            dailytask_id: formtask.value.dailytask_id,
            task_name: formtask.value.task_name,
            iscompleted: formtask.value.iscompleted,
        })
        .then((response) => {
            // $("#FormModalTask").modal("hide");
            toastr.success("Data created successfully!");
            // Fetch tasks using GET request after the POST request is successful
            axios
                .get(`/api/dailytask/${formtask.value.dailytask_id}/tasks`)
                .then((response) => {
                    listasks.value = response.data;
                    isloadingTask.value = false;
                })
                .catch((error) => {
                    console.error("Error fetching tasks:", error);
                    toastr.error("Error fetching tasks. Please try again.");
                });

            formtask.value.task_name = "";
        })
        .catch((error) => {
            console.error("Error adding new task:", error);
            toastr.error("Error adding new task. Please try again.");
        });
};
// click task item to complete or cancel
const handleCompleteTask = (item) => {
    // Assuming formtask is a ref
    formtask.value.iscompleted = item.iscompleted === 1 ? 0 : 1;

    // Continue with the POST request
    axios
        .post("/api/dailytask/addnewTask", {
            id: item.id,
            dailytask_id: item.dailytask_id,
            task_name: item.task_name,
            iscompleted: formtask.value.iscompleted,
        })
        .then((response) => {
            // Fetch tasks using GET request after the POST request is successful
            axios
                .get(`/api/dailytask/${item.dailytask_id}/tasks`)
                .then((response) => {
                    listasks.value = response.data;
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

const completedTasks = computed(() => {
    if (!Array.isArray(listasks.value)) {
        return [];
    }

    return listasks.value.filter((item) => item.iscompleted === 1);
});

const completedTaskCount = computed(() => completedTasks.value.length);

const toggleList = () => {
    showList.value = !showList.value;
};

const delTask = (item) => {
    // Continue with the POST request
    axios
        .delete(`/api/dailytask/deleteTask/${item.id}`)
        .then((response) => {
            // Fetch tasks using GET request after the POST request is successful
            axios
                .get(`/api/dailytask/${item.dailytask_id}/tasks`)
                .then((response) => {
                    listasks.value = response.data;
                    toastr.success("Todo successfull Deleted");
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

//end modal task
const createDataSchema = yup.object({
    site: yup.string().required(),
    tasktype: yup.string().required(),
    plandate: yup.string().required(),
    planenddate: yup.string().required(),
});

const editDataSchema = yup.object({
    site: yup.string().required(),
    tasktype: yup.string().required(),
    plandate: yup.string().required(),
    planenddate: yup.string().required(),
});

const createData = (values, actions) => {
    axios
        .post("/api/dailytask", form)
        .then((response) => {
            console.log(response.data);
            getItems();
            $("#FormModal").modal("hide");
            toastr.success("data created successfully!");
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        });
};

const updateData = (values, actions) => {
    axios
        .put("/api/dailytask/" + form.dailytask_id, form)
        .then((response) => {
            console.log(response.data);
            getItems();
            $("#FormModal").modal("hide");
            toastr.success("data update successfully!");
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        });
};
const addTask = (value) => {
    if (
        value &&
        value.dailytask_id !== null &&
        value.dailytask_id !== undefined
    ) {
        editing.value = true;
        currentdate.value =
            " on " + moment(value.taskdate).format("MMMM D, YYYY");
        form.site = value.site;
        form.dailytask_id = value.dailytask_id;
        form.plandate = value.plandate;
        form.planenddate = value.planenddate;

    } else {
        editing.value = false;
    }
    getSite();
    $("#FormModal").modal("show");
};


const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateData(values, actions);
    } else {
        createData(values, actions);
    }
};

//start Prio
const startTaskhandle = async (task) => {
    try {
        // Fetch tasks using GET request after the POST request is successful
        const response = await axios.get(
            `/api/dailytask/${task.dailytask_id}/tasks`
        );
        listasks.value = response.data;

        // Check if listtasks is null
        if (!listasks.value || listasks.value.length === 0) {
            // Show a warning message using SweetAlert2
            swal.fire({
                title: "Warning!",
                text: "No todos found. Please add todo before starting your task.",
                icon: "warning",
            });
            return; // Exit the function early
        }

        // Show the SweetAlert2 dialog for confirmation
        const result = await swal.fire({
            title: "Are you sure?",
            text: "You want to start your todo now?",
            icon: "warning",
            showCancelButton: true,
        });

        // Check if the user confirmed
        if (result.isConfirmed) {
            // Make the axios PUT request
            const updateResponse = await axios.put(
                `/api/dailytask/onhandler/` + task.dailytask_id,
                task
            );

            // Handle the response
            toastr.success(updateResponse.data.message);

            // Refresh your data or perform any other actions
            getItems();
        }
    } catch (error) {
        console.error("Error fetching tasks:", error);
        toastr.error("An error occurred while updating the task.");
    }
};

//end start
const endTaskhandle = async (task) => {
    // Show the SweetAlert2 dialog
    const result = await swal({
        title: "Are you sure?",
        text: "You wanna end your task now?",
        icon: "warning",
        showCancelButton: true,
    });

    // Check if the user confirmed
    if (result.isConfirmed) {
        try {
            const endstart =
                moment(task.taskdate).format("YYYY-MM-DD") ===
                moment().format("YYYY-MM-DD");

            if (endstart) {
                // If taskdate is the same as the current date and time, set status to "HIT"
                task.remarks = "HIT";
                task.status = "On Schedule";
            } else {
                // If taskdate is different from the current date and time, set status to "MISS"
                task.remarks = "MISS";
                task.status = "Behind Schedule";
            }
            // Make the axios PUT request
            const response = await axios.put(
                `/api/dailytask/onhandler/` + task.dailytask_id,
                task
            );

            // Handle the response
            toastr.success(response.data.message);

            // Refresh your data or perform any other actions
            getItems();
        } catch (error) {
            console.error(error);
            // Handle the error if needed
            toastr.error("An error occurred while updating the task.");
        }
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

const toggleTaskList = () => {
    showTaskList.value = !showTaskList.value;
};

watch(
    searchQuery,
    debounce(() => {
        getItems();
    }, 300)
);

onMounted(() => {
    flatpickr(".flatpickr", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K",
        defaultHour: 10,
    });
    getItems();
    document.title = pageTitle;

});
</script>

<template>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        {{
                            authUserStore.user.first_name +
                            " " +
                            authUserStore.user.last_name
                        }}
                        - My Prio
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <button
                                    @click="addTask"
                                    type="button"
                                    class="mb-2 btn btn-primary"
                                >
                                    <i class="fa fa-plus-circle mr-1"></i>
                                    Task
                                </button>
                            </div>
                            <div class="d-flex">
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    class="form-control"
                                    placeholder="Search..."
                                />
                            </div>
                        </div>
                        <div class="col-12" id="accordion">
                            <ContentLoader v-if="isloading"/>
                            <div v-else>
                                <div v-if="lists.length === 0">
                                    <!-- Show this card when the list is empty -->
                                    <div class="card card-secondary">
                                        <div
                                            class="card-body d-flex align-items-center justify-content-center"
                                        >
                                            <div class="image-container">
                                                <img
                                                    :src="'/img/no task.jpg'"
                                                    alt="No Task"
                                                    class="img-fluid"
                                                    draggable="false"
                                                />
                                                <div class="overlay"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div
                                        class="card card-primary"
                                        v-for="task in lists"
                                        :key="task.id"
                                    >
                                        <div class="card-header bg-white">
                                            <h4 class="card-title">
                                                <a
                                                    style="
                                                        color: #2b2b2b;
                                                        text-decoration: none;
                                                    "
                                                    data-toggle="collapse"
                                                    :href="
                                                        '#collapse' +
                                                        task.dailytask_id
                                                    "
                                                >
                                                    <!-- <i
                                                        class="fas fa-calendar-alt"
                                                    ></i

                                                    >-->
                                                    <img
                                                        :src="'/img/calindar_logo.png'"
                                                        alt=""
                                                        class="brand-image"
                                                        style="opacity: 0.8"
                                                        draggable="false"
                                                        width="35"
                                                    />
                                                    &nbsp;<b>{{
                                                        moment(
                                                            task.taskdate
                                                        ).format("MMMM D, YYYY")
                                                    }}</b>
                                                </a>
                                            </h4>
                                            <div class="card-tools">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-primary float-right"
                                                    style="margin-left: 10px"
                                                    @click="showTasks(task)"
                                                >
                                                    <i class="fas fa-tasks"></i>
                                                </button>
                                                <button
                                                    :disabled="
                                                        task.startdate === null
                                                    "
                                                    type="button"
                                                    class="btn btn-sm btn-danger float-right"
                                                    style="margin-left: 10px"
                                                    @click="endTaskhandle(task)"
                                                >
                                                    End
                                                </button>

                                                <button
                                                    v-if="!task.startdate"
                                                    type="button"
                                                    class="btn btn-sm btn-success float-right"
                                                    style="margin-left: 10px"
                                                    @click="
                                                        startTaskhandle(task)
                                                    "
                                                >
                                                    Start
                                                </button>
                                                <p class="float-right"></p>
                                            </div>
                                        </div>

                                        <div
                                            :id="'collapse' + task.dailytask_id"
                                            class="collapse"
                                            data-parent="#accordion"
                                        >
                                            <div class="card-body">
                                                <div class="col-md-12">
                                                    <div class="dispatch-table">
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <h5>
                                                                    <b>Site:</b>
                                                                    {{
                                                                        task.site_name
                                                                    }}
                                                                </h5>
                                                                <!-- <h5>
                                                        <b>Project:</b>
                                                        {{ task.project }}
                                                    </h5> -->
                                                                <h5>
                                                                    <b
                                                                        >Planned
                                                                        Date:</b
                                                                    >
                                                                    {{
                                                                        task.plandate
                                                                    }}
                                                                </h5>
                                                                <h5>
                                                                    <b
                                                                        >Planned
                                                                        End
                                                                        Date:</b
                                                                    >
                                                                    {{
                                                                        task.planenddate
                                                                    }}
                                                                </h5>
                                                            </div>

                                                            <div class="col-4">
                                                                <h5>
                                                                    <b
                                                                        >Start
                                                                        Date:</b
                                                                    >
                                                                    {{
                                                                        task.startdate !==
                                                                        null
                                                                            ? moment(
                                                                                  task.startdate
                                                                              ).format(
                                                                                  "MMMM D, YYYY, h:mm A"
                                                                              )
                                                                            : ""
                                                                    }}
                                                                </h5>
                                                                <h5>
                                                                    <b
                                                                        >Accomplished
                                                                        Date:</b
                                                                    >
                                                                    {{
                                                                        task.enddate !==
                                                                        null
                                                                            ? moment(
                                                                                  task.enddate
                                                                              ).format(
                                                                                  "MMMM D, YYYY, h:mm A"
                                                                              )
                                                                            : ""
                                                                    }}
                                                                </h5>

                                                                <h5
                                                                    class="closestatus"
                                                                >
                                                                    <b
                                                                        style="
                                                                            color: black;
                                                                        "
                                                                        >Type:</b
                                                                    >
                                                                    {{
                                                                        task.tasktype
                                                                            ? task.tasktype.join(
                                                                                  ", "
                                                                              )
                                                                            : ""
                                                                    }}
                                                                </h5>
                                                            </div>
                                                            <div class="col-4">
                                                                <h5
                                                                    class="ongoing"
                                                                >
                                                                    <b
                                                                        style="
                                                                            color: black;
                                                                        "
                                                                        >Status:</b
                                                                    >
                                                                    {{
                                                                        task.status
                                                                    }}
                                                                </h5>
                                                                <h5>
                                                                    <b
                                                                        style="
                                                                            color: black;
                                                                        "
                                                                        >Attachment:</b
                                                                    >
                                                                    {{
                                                                        task.attachment
                                                                    }}
                                                                </h5>
                                                                <!-- <h5 class="closestatus">
                                                        <b style="color: black">PWS:</b>
                                                        {{ task.PWS }}
                                                    </h5> -->

                                                                <h5>
                                                                    <b
                                                                        >Remarks:</b
                                                                    >
                                                                    {{
                                                                        task.remarks
                                                                    }}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div
                                                            style="margin: 0.5%"
                                                        >
                                                            <button
                                                                :disabled="
                                                                    task.startdate !==
                                                                    null
                                                                "
                                                                @click="
                                                                    drop(
                                                                        task.dailytask_id
                                                                    )
                                                                "
                                                                type="button"
                                                                class="btn btn btn-danger float-right fa fa-trash"
                                                                style="
                                                                    margin-left: 10px;
                                                                    margin-bottom: 5px;
                                                                "
                                                            >
                                                                Drop</button
                                                            ><button
                                                                @click="
                                                                    addTask(
                                                                        task
                                                                    )
                                                                "
                                                                type="button"
                                                                :disabled="
                                                                    task.startdate !==
                                                                    null
                                                                "
                                                                class="btn btn btn-primary far fa-edit float-left"
                                                                style="
                                                                    margin-right: 5px;
                                                                    margin-bottom: 5px;
                                                                "
                                                            >
                                                                &nbsp;&nbsp;Edit
                                                            </button>
                                                            <!-- <button
                                                                type="button"
                                                                class="btn btn float-left fa fa-file btn-primary"
                                                                style="
                                                                    margin-right: 5px;
                                                                    margin-bottom: 5px;
                                                                "
                                                            >
                                                                &nbsp;&nbsp;Attachment
                                                            </button> -->
                                                        </div>
                                                    </div>
                                                    <div class="dispatch-list">
                                                        <ul
                                                            class="list-group list-group-flush"
                                                        >
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Site:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                    >{{
                                                                        task.site_name
                                                                    }}</span
                                                                >
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Planned Date:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                >
                                                                    {{
                                                                        task.plandate
                                                                    }}</span
                                                                >
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Planned End
                                                                Date:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                >
                                                                    {{
                                                                        task.planenddate
                                                                    }}</span
                                                                >
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Start Date:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                    >{{
                                                                        task.startdate !==
                                                                        null
                                                                            ? moment(
                                                                                  task.startdate
                                                                              ).format(
                                                                                  "MMMM D, YYYY, h:mm A"
                                                                              )
                                                                            : ""
                                                                    }}</span
                                                                >
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Type:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                >
                                                                    {{
                                                                        task.tasktype
                                                                            ? task.tasktype.join(
                                                                                  ", "
                                                                              )
                                                                            : ""
                                                                    }}</span
                                                                >
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Status:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                >
                                                                    {{
                                                                        task.status
                                                                    }}</span
                                                                >
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Attachment:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                >
                                                                    {{
                                                                        task.attachment
                                                                    }}</span
                                                                >
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                            >
                                                                Remarks:
                                                                <span
                                                                    class="badge badge-primary badge-pill"
                                                                    >{{
                                                                        task.remarks
                                                                    }}</span
                                                                >
                                                            </li>
                                                        </ul>
                                                        <hr />
                                                        <div class="row">
                                                            <div class="col">
                                                                <button
                                                                    variant="primary"
                                                                    class="btn btn-sm btn-primary btn-block"
                                                                    @click="
                                                                        addTask(
                                                                            task
                                                                        )
                                                                    "
                                                                >
                                                                    <i
                                                                        class="fas fa-pen"
                                                                    ></i>
                                                                    Edit
                                                                </button>
                                                                <button
                                                                    class="btn btn-sm btn-danger btn-block"
                                                                    @click="
                                                                        drop(
                                                                            task.dailytask_id
                                                                        )
                                                                    "
                                                                >
                                                                    <span
                                                                        class="text-light"
                                                                    >
                                                                        <i
                                                                            class="fas fa-trash"
                                                                        ></i>
                                                                        Drop
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit My Task</span>
                        <span v-else>Add My Task</span>
                        <span v-if="editing"> {{ currentdate }}</span>
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

                <Form @submit="handleSubmit">
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
                                        <label for="siteDropdown"
                                            >Select a Site:</label
                                        >
                                        <select
                                            class="form-control"
                                            id="siteDropdown"
                                            v-model="form.site"
                                        >
                                            <option value="" disabled>
                                                Select a site
                                            </option>
                                            <option
                                                v-for="site in listsite"
                                                :key="site.id"
                                                :value="site.id"
                                            >
                                                {{ site.site_name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div
                                        v-if="editing == false"
                                        class="d-flex justify-content-between"
                                    >
                                        <div class="d-flex">
                                            <div class="form-group">
                                                <label for="end-time"
                                                    >Start Date</label
                                                >
                                                <datepicker
                                                    class="form-control"
                                                    v-model="Sdate"
                                                ></datepicker>
                                            </div>

                                            <div class="form-group">
                                                <!-- Dropdown for selecting hours -->
                                                <label for="end-time"
                                                    >Hour</label
                                                >
                                                <select
                                                    class="form-control"
                                                    v-model="StrHours"
                                                    id="plandate"
                                                >
                                                    <option
                                                        v-for="hour in starthours"
                                                        :key="hour"
                                                        :value="hour"
                                                    >
                                                        {{ hour }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-fex"></div>
                                    </div>

                                    <div
                                        v-if="editing == false"
                                        class="d-flex justify-content-between"
                                    >
                                        <div class="d-flex">
                                            <div class="form-group">
                                                <label for="end-time"
                                                    >End Date</label
                                                >
                                                <datepicker
                                                    class="form-control"
                                                    v-model="Edate"
                                                ></datepicker>
                                            </div>
                                            <div class="form-group">
                                                <!-- Dropdown for selecting hours -->
                                                <label for="end-time"
                                                    >Hour</label
                                                >
                                                <select
                                                    class="form-control"
                                                    v-model="EndHours"
                                                    id="plandate"
                                                >
                                                    <option
                                                        v-for="hour in endhours"
                                                        :key="hour"
                                                        :value="hour"
                                                    >
                                                        {{ hour }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-fex"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="site">Task</label>
                                        <Field name="tasktype">
                                            <select
                                                v-model="form.tasktype"
                                                class="form-control"
                                                :required="true"
                                            >
                                                <option value="" disabled>
                                                    Select an option
                                                </option>
                                                <option
                                                    v-for="option in taskoptions"
                                                    :key="option.value"
                                                    v-bind:value="option.value"
                                                >
                                                    {{ option.name }}
                                                </option>
                                            </select>
                                        </Field>
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
                            submit
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="FormModalTask"
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
                        My Todo of {{ moment(taskdate).format("MM/D/YYYY") }}
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
                    <ul class="nav nav-tabs" id="myTabs">
                        <li class="nav-item">
                            <a
                                class="nav-link btn btn-primary active"
                                id="tab1"
                                data-toggle="tab"
                                href="#todolist"
                            >
                                Todo List</a
                            >
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link btn btn-primary"
                                id="tab2"
                                data-toggle="tab"
                                href="#formTask"
                            >
                                Todo</a
                            >
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Tab 1: Task List -->
                        <div class="tab-pane fade show active" id="todolist">
                            <div
                                class="mt-2"
                                v-if="listasks.length > 0"
                                style="max-height: 300px; overflow-y: auto"
                            >
                                <!-- Separate List for incomplete tasks -->

                                <ContentLoader
                                    v-if="isloadingTask"
                                    viewBox="0 0 250 110"
                                >
                                    <rect
                                        x="0"
                                        y="0"
                                        rx="3"
                                        ry="3"
                                        width="250"
                                        height="50"
                                    />

                                    <rect
                                        x="0"
                                        y="0"
                                        rx="3"
                                        ry="3"
                                        width="250"
                                        height="50"
                                    />
                                </ContentLoader>
                                <ul
                                    class="list-group"
                                    v-for="item in listasks"
                                    :key="item.id"
                                >
                                    <li
                                        v-if="item.iscompleted !== 1"
                                        class="list-group-item mt-2"
                                    >
                                        <div
                                            class="d-flex justify-content-between"
                                        >
                                            <div class="d-flex">
                                                <i
                                                    @click="
                                                        handleCompleteTask(item)
                                                    "
                                                    v-if="startdate"
                                                    :class="{
                                                        'cursor-pointer mr-2': true,
                                                        'fa fa-check-circle text-primary':
                                                            item.iscompleted ===
                                                            1,
                                                        'fa fa-circle text-primary':
                                                            item.iscompleted !==
                                                            1,
                                                    }"
                                                    style="font-size: 15px"
                                                ></i>

                                                <span
                                                    :class="{
                                                        'font-italic':
                                                            item.iscompleted ===
                                                            1,
                                                        '':
                                                            item.iscompleted !==
                                                            1,
                                                    }"
                                                >
                                                    {{ item.task_name }}
                                                </span>
                                            </div>
                                            <div class="d-flex">
                                                <i
                                                    class="fa fa-trash text-danger"
                                                    @click="delTask(item)"
                                                ></i>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                <!-- List for completed tasks -->
                                <li class="m-2 list-unstyled">
                                    <button
                                        class="btn btn-sm bg-secondary"
                                        @click="toggleList"
                                        v-if="completedTaskCount > 0"
                                    >
                                        <i
                                            :class="[
                                                'fa',
                                                showList
                                                    ? 'fa-arrow-down'
                                                    : 'fa-arrow-right',
                                            ]"
                                        ></i>
                                        &nbsp;Completed {{ completedTaskCount }}
                                    </button>
                                </li>
                                <div v-if="showList">
                                    <ul
                                        class="list-group"
                                        v-for="item in listasks"
                                        :key="item.id"
                                    >
                                        <li
                                            v-if="item.iscompleted === 1"
                                            class="list-group-item mt-2"
                                        >
                                            <div
                                                class="d-flex justify-content-between"
                                            >
                                                <div class="d-flex">
                                                    <i
                                                        :class="{
                                                            'cursor-pointer mr-2': true,
                                                            'fa fa-check-circle':
                                                                item.iscompleted ===
                                                                1,
                                                            'fa fa-circle':
                                                                item.iscompleted !==
                                                                1,
                                                        }"
                                                        style="font-size: 15px"
                                                        @click="
                                                            handleCompleteTask(
                                                                item
                                                            )
                                                        "
                                                    ></i>

                                                    <span
                                                        :class="{
                                                            'font-italic':
                                                                item.iscompleted ===
                                                                1,
                                                            '':
                                                                item.iscompleted !==
                                                                1,
                                                        }"
                                                    >
                                                        <del>{{
                                                            item.task_name
                                                        }}</del>
                                                    </span>
                                                </div>
                                                <div class="d-flex">
                                                    <i
                                                        class="fa fa-trash text-danger"
                                                        @click="delTask(item)"
                                                    ></i>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-2 text-center" v-else>
                                <span>No Todo</span>
                                <!-- <img :src="imageUrl" alt="No Task" /> -->
                            </div>
                        </div>
                        <!-- Tab 2: Form Task -->
                        <div class="tab-pane fade" id="formTask">
                            <Form @submit="AddNewTask">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-12">
                                            <Field
                                                v-model="formtask.dailytask_id"
                                                type="hidden"
                                                name="dailytask_id"
                                                id="dailytask_id"
                                            />
                                            <div
                                                class="m-2"
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                "
                                            >
                                                <Field
                                                    v-model="formtask.task_name"
                                                    name="'task_name"
                                                    type="text"
                                                    class="form-control"
                                                    required
                                                    placeholder="Enter Task"
                                                    style="margin-right: 5px"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="border: none">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-dismiss="modal"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        <i class="fas fa-plus-circle"></i
                                        >&nbsp;Save
                                    </button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
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
                        Delete User
                    </button>
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
    color: #fff;
    transition: background-color 0.3s;
}

.nav-tabs .nav-link.active {
   background-color: #0069d9;
    color: #fff;
    border-color: #2196f3;
}

.nav-tabs .nav-link:hover {
    background-color: #0069d9;
    border-color: #0069d9;
     color: #fff;
}

.image-container {
    position: relative;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: transparent;
    z-index: 1;
}
</style>
