<script setup>
import { onMounted, ref, watch } from "vue";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import moment from "moment";
import ListItem from "./ListItem.vue";
import html2canvas from "html2canvas";
import ContentLoader from "../../components/ContentLoader.vue";
import Datepicker from "vue3-datepicker";
import { useRoute } from "vue-router";

const pageTitle = `${useRoute().name}`;

const authUserStore = useAuthUserStore();

const isloading = ref(false);



const themcolor = ref([
    {
        background: "#B98D65",
        active_background: "#72461F",
        font_background: "#F8F9FA",
    },
    {
        background: "#2196F3",
        active_background: "#1769AA",
        font_background: "#F8F9FA",
    },
     {
        background: "#FFC400",
        active_background: "#B28900",
        font_background: "#F8F9FA",
    },
    {
        background: "#9C27B0",
        active_background: "#6D1B7B",
        font_background: "#F8F9FA",
    },

    {
        background: "#009688",
        active_background: "#00695F",
        font_background: "#F8F9FA",
    },
    {
        background: "#607d8b",
        active_background: "#37474f",
        font_background: "#F8F9FA",
    },

]);
//format date
const getFormattedDate = () => {
    const options = { month: "long", day: "numeric", year: "numeric" };
    return new Date().toLocaleString("en-US", options);
};
const formattedDate = ref(getFormattedDate());
//end format date

const isCurrentDate = (taskDate) => {
    const currentDate = new Date().toISOString().split("T")[0];
    return taskDate === currentDate;
};

//capture function
const capturevsc = () => {
    const container = document.getElementById("captureVSCContainer");

    html2canvas(container).then((canvas) => {
        const dataURL = canvas.toDataURL();
        const link = document.createElement("a");
        link.href = dataURL;
        link.download = `${ authUserStore.user.first_name +
                                    " " +
                                    authUserStore.user.last_name} - ${pageTitle}.png`;
        link.click();
    });
};

const capturehitrate = () => {
    const container = document.getElementById("captureHitRateContainer");

    html2canvas(container).then((canvas) => {
        const dataURL = canvas.toDataURL();
        const link = document.createElement("a");
        link.href = dataURL;
        link.download = `${ authUserStore.user.first_name +
                                    " " +
                                    authUserStore.user.last_name} - My HIT RATE.png`;
        link.click();
    });
};

const containercapture = ref(null);
const selectedDateRange = ref("today");
const lists = ref({ data: [] });
const listscount = ref({ data: [] });
const getItems = () => {
    isloading.value = true;
    axios.get(`/api/myvsc`).then((response) => {
        isloading.value = false;
        lists.value = response.data.dailyTasks;
        listscount.value = response.data.TaskList;
    });
};

const onFilterDate = () => {
    $("#FormModalfilterDate").modal("show");
};

// Create a reactive form object

const form = ref({
    start_date: "",
    end_date: "",
});

const fromDate = ref("");
const toDate = ref("");

// Watch for changes in Sdate and StrHours and update plandate
watch([fromDate], () => {
    const originalDate = new Date(fromDate.value);
    const year = originalDate.getFullYear();
    const month = String(originalDate.getMonth() + 1).padStart(2, "0");
    const day = String(originalDate.getDate()).padStart(2, "0");
    form.value.start_date = `${year}-${month}-${day}`;
});

// Watch for changes in Edate and EndHours and update planenddate
watch([toDate], () => {
    const originalDate = new Date(toDate.value);
    const year = originalDate.getFullYear();
    const month = String(originalDate.getMonth() + 1).padStart(2, "0");
    const day = String(originalDate.getDate()).padStart(2, "0");
    form.value.end_date = `${year}-${month}-${day}`;
});

const applyFilter = () => {


    isloading.value = true;

    axios
        .get("/api/filter-vsc", {
             params:{
                start_date: form.value.start_date,
                end_date: form.value.end_date,
            }

        })
        .then((response) => {
            isloading.value = false;
            lists.value = response.data.dailyTasks;
            listscount.value = response.data.TaskList;

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

const onPickThemes = () => {
    $("#FormModalPickerThemes").modal("show");
}

const handleThemeClick = (item) => {
  if (item) {
        const userId = authUserStore.user.id;

        axios.post('/api/changethemes', {

            userid: userId,
            background: item.background,
            active_background: item.active_background,
            font_background: item.font_background
        })
        .then(response => {
            $("#FormModalPickerThemes").modal("hide");
           isloading.value = true;
            axios.get(`/api/myvsc`).then((response) => {
                isloading.value = false;
                lists.value = response.data.dailyTasks;
                listscount.value = response.data.TaskList;
            });
        })
        .catch(error => {
            console.error(error);
        });
    } else {
        console.error('Invalid item:', item);
    }
}

onMounted(() => {
    getItems();
    document.title = pageTitle;

});
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card" id="captureVSCContainer">
                    <div class="card-header">
                        <div class="card-title">
                            <h5>
                                {{
                                    authUserStore.user.first_name +
                                    " " +
                                    authUserStore.user.last_name
                                }}
                                - {{pageTitle}}
                            </h5>
                        </div>

                        <div class="card-tools">
                            <i @click="onPickThemes()" class='fas fa-palette'></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <button
                                    @click="capturevsc"
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
                        <ContentLoader v-if="isloading"/>
                        <div v-else class="row">
                            <div
                                class="col-lg-3 col-6"
                                v-for="task in lists"
                                :key="task.id"
                            >

                                <div class="small-box" :style="'background-color: '+(moment(task.taskdate).format(
                                            'MMMM D, YYYY'
                                        ) === formattedDate
                                            ? (task.active_background == '' ? '#72461F' : task.active_background)
                                            : (task.background == '' ? '#B98D65' : task.background)) +';'"
                                >
                                    <div class="inner">

                                        <div style="font-size:20px; font-family:tahoma; text-align:center;background:white; color:#000000;margin-bottom:2px">{{
                                                    moment(
                                                        task.taskdate
                                                    ).format("dddd")
                                                }}
                                            <img :src="'/img/calindar_logo.png'"
                                                    alt=""
                                                    class="brand-image"
                                                    style="opacity: 0.8"
                                                    draggable="false" width="35">
                                                <!-- <i
                                                    class="far fa-calendar-alt"
                                                ></i> -->
                                                </div>
                                        <div style="text-align:center; background:white;color:#000000; margin-bottom:2px;">  {{
                                                    moment(
                                                        task.taskdate
                                                    ).format("MMMM D, YYYY")
                                                }}</div>


                                            <div class="m-2" style="color:#F3F5F8; text-align:left;">
                                                {{ task.site_name }}
                                            </div>
                                        <div  style="text-align:left;">
                                            <div
                                                v-if="
                                                    task.task_lists &&
                                                    task.task_lists.length > 0
                                                "
                                            >
                                                <span class="text-light badge">Todos</span>
                                              <ul class="list-group list-group-flush">
                                                    <li
                                                        class="list-group-item"
                                                        v-for="taskList in task.task_lists"
                                                        :key="taskList.id"
                                                    >
                                                    <div style="display: flex; align-items: left;">
                                                         <div class="ml-1">
                                                         <i
                                                            :class="
                                                                taskList.iscompleted ==
                                                                1
                                                                    ? 'fa fa-check-circle'
                                                                    : 'fa fa-circle'
                                                            "
                                                            style="
                                                                font-size: 15px;
                                                            "
                                                        ></i>
                                                    </div>

                                                        <div class="ml-1">{{
                                                            taskList.task_name
                                                        }}</div>
                                                    </div>

                                                    </li>
                                                </ul>
                                            </div>

                                            <span v-else
                                                ></span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      <div class="float-right">   {{ formattedDate }}</div>
                    </div>
                </div>

                <!-- Hit rate -->
                <div id="captureHitRateContainer" class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h5>
                                {{
                                    authUserStore.user.first_name +
                                    " " +
                                    authUserStore.user.last_name
                                }}
                                - My HIT RATE
                            </h5>
                        </div>


                    </div>

                    <div class="card-body">
                        <div class="content">
                            <div class="container-fluid">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex">
                                        <button
                                            @click="capturehitrate"
                                            type="button"
                                            class="mb-2 btn btn-sm btn-success"
                                        >
                                            <i class="fa fa-camera"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="dispatch-table">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-bordered table-hover table-sm"
                                        >
                                            <thead>
                                                <tr>
                                                    <th>Planed Date</th>
                                                    <th>Total Todos</th>
                                                    <th>Complete</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Percentage Todos</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <ListItem
                                                    v-for="item in listscount"
                                                    :key="item.id"
                                                    :item="item"
                                                />
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div
                                    class="dispatch-list"
                                    v-for="item in listscount"
                                    :key="item.id"
                                    :item="item"
                                >
                                    <div
                                        :class="[
                                            {
                                                'callout callout-success':
                                                    item.remarks === 'HIT',
                                                'callout callout-danger':
                                                    item.remarks === 'MISS',
                                                callout: item.remarks === null,
                                            },
                                        ]"
                                    >
                                        <div class="list-field">
                                            <span class="mb-1 dis"
                                                >Planed Date:
                                            </span>
                                            <span>{{
                                                moment(item.taskdate).format(
                                                    "MMMM D, YYYY"
                                                )
                                            }}</span>
                                        </div>
                                        <div class="list-field">
                                            <span class="mb-1 dis"
                                                >Total Task:
                                            </span>
                                           <span v-if="item.task_lists_count !== 0">{{ item.task_lists_count }}</span>
                                        </div>
                                        <div class="list-field">
                                            <span class="mb-1 dis"
                                                >Completed Task:
                                            </span>
                                            <span v-if="item.completed_task_count !== 0">{{ item.completed_task_count }}</span>
                                        </div>
                                        <div class="list-field">
                                            <span class="mb-1 dis"
                                                >Status:
                                            </span>
                                            <span
                                                ><b>{{ item.status }}</b></span
                                            >
                                        </div>
                                        <div class="list-field">
                                            <span class="mb-1 dis"
                                                >Remark:
                                            </span>
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
                                        <div class="list-field">
                                            <span class="mb-1 dis"
                                                >Percentage Task:
                                            </span>
                                            <span
                                                :class="[
                                                    'badge',
                                                    {
                                                        'bg-danger':
                                                            item.percentage_completed >=
                                                                0 &&
                                                            item.percentage_completed <=
                                                                59,
                                                    },
                                                    {
                                                        'bg-orange':
                                                            item.percentage_completed >
                                                                60 &&
                                                            item.percentage_completed <=
                                                                90,
                                                    },
                                                    {
                                                        'bg-success':
                                                            item.percentage_completed ===
                                                            100,
                                                    },

                                                ]"
                                            >
                                                {{
                                                    item.percentage_completed +
                                                    "%"
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="float-right">   {{ formattedDate }}</div>
                    </div>
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
                        <span>{{pageTitle}} Summary Report</span>
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

     <div
        class="modal fade"
        id="FormModalPickerThemes"
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
                        <span>Card Themes Picker</span>
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
                    <div class="container">
                        <div class="row" >
                        <div class="col-md-4" v-for="item in themcolor" :key="item.background">
                            <div class="box" @click="handleThemeClick(item)">
                             <div class="column" :style="{ 'background-color': item.background }">&nbsp;</div>
                            <div class="column" :style="{ 'background-color': item.active_background }">&nbsp;</div>

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
