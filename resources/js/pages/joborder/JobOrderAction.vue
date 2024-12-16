<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import { debounce } from "lodash";
import * as yup from "yup";
import { useToastr } from "@/toastr.js";
import ContentLoader from "@/components/ContentLoader.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import { useRoute } from "vue-router";
import FormTextField from "@/components/FormTextField.vue";
import FormSelectOption from "@/components/FormSelectOption.vue";
import FormTextArea from "@/components/FormTextAria.vue";
const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const isloading = ref(false);
import { useAuthUserStore } from "@/stores/AuthUserStore";
const formValues = ref();

const authUserStore = useAuthUserStore();
const checked = ref(true);
const form = reactive({
    id: "",
    site_id: null,
    site_name: "",
    job_order_number: "",
    end_user: "",
    time_requested: "",
    date_needed: "",
    noted_by: "",
    type_of_job: "",
    problem_description: "",
    findings_recommendations: "",
    commitment_date: "",
    status: "P",
    created_by: authUserStore.user.id,
    updated_by: "",

    description:"",
    part_number:"",
    quantity:""
});

const selectedStatus = ref(null);

const listsite = ref([]);

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

const listjobs = ref([
 { id: 1, name: 'Preventive Maintenance' },
 { id: 2, name: 'Corrective Maintenance' }, 
 { id: 2, name: 'Calibration' },
]);


const createData = () => {
    axios
        .post("/web/job-request", form)
        .then((response) => {
            toastr.success(response.data.message);
        })
        .catch((error) => {
            toastr.error(error.data.message);
        });
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
  
        <div class="col-md-12">
            <form-wizard
                @on-complete="createData"
                color="#094899"
                step-size="xs"
            >
                <tab-content title="Job Information" icon="fas fa-book">

                    <FormTextField
                        label="Job Order Number"
                        placeholder="Job Order Number - AutoGenerate"
                        name="job_order_number"
                        type="text"
                        id="job_order_number"
                        v-model="form.job_order_number"
                        :errors="errors.job_order_number"
                    />
                    

                    <FormSelectOption
                        name="branch"
                        label="Area / Department"
                        v-model="form.site_id"
                        :options="listsite"
                        :errors="errors.branch"
                    />

            
                    <FormTextField
                        label="End User"
                        placeholder="End User"
                        name="enduser"
                        type="text"
                        id="enduser"
                        v-model="form.end_user"
                        :errors="errors.end_user"
                    />

                    <FormTextField
                        label="Time Requested"
                        placeholder="Time Requested"
                        name="time_requested"
                        type="date"
                        id="time_requested"
                        v-model="form.time_requested"
                        :errors="errors.time_requested"
                    />

                    <FormTextField
                        label="Date needed"
                        placeholder="Date needed"
                        name="date_needed"
                        type="date"
                        id="date_needed"
                        v-model="form.date_needed"
                        :errors="errors.date_needed"
                    />
                    <FormTextField
                        label="Noted By"
                        placeholder="Noted By"
                        name="noted_by"
                        type="text"
                        id="noted_by"
                        v-model="form.noted_by"
                        :errors="errors.noted_by"
                    />
                    <FormSelectOption
                        name="type_of_job"
                        label="Type of Job"
                        v-model="form.type_of_job"
                        :options="listjobs"
                        :errors="errors.type_of_job"
                    />
                   
                </tab-content>

                <tab-content title="Problem Details" icon="fas fa-pen-alt">

                    <FormTextArea
                                label="Problem Description"
                                id="problem_description"
                                name="problem_description"
                                rows="3"
                                placeholder="Problem Description"
                                v-model="form.problem_description"
                    />
                   

                    <FormTextArea
                                label="Recommendations"
                                id="Recommendations"
                                rows="3"
                                placeholder="Recommendations"
                                v-model="form.findings_recommendations"
                    />
                 
                    <FormTextField
                        label="Commitment Date"
                        placeholder="Commitment Date"
                        name="commitment_date"
                        type="date"
                        id="commitment_date"
                        v-model="form.commitment_date"
                        :errors="errors.commitment_date"
                    />
                </tab-content>
                <tab-content title="Replacement Parts" icon="fas fa-tools">
                    <div class="form-group row">
                        <label
                            for="replacementParts"
                            class="col-sm-2 col-form-label"
                            >Replacement Parts</label
                        >
                        <div class="col-sm-10">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Part Number</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Description"
                                                 v-model="form.description"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Part Number"
                                                 v-model="form.part_number"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                class="form-control"
                                                placeholder="Quantity"
                                                 v-model="form.quantity"
                                            />
                                        </td>
                                    </tr>
                                    <!-- Add more rows as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </tab-content>
            </form-wizard>
        </div>
   
</template>
