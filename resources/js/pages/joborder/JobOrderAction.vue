<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch ,inject} from "vue";
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
const swal = inject("$swal");
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
    quantity:"",

    replacement_parts: [
    {
        description: '',
        part_number: '',
        quantity: 0,
    },]

});

const addPart = ()=>{
    form.replacement_parts.push({
        description: '',
        part_number: '',
        quantity: 0,
    });

}

const removePart =(index)=>{
    if(form.replacement_parts.length >1){
    form.replacement_parts.splice(index, 1);
    }
}
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
 { id: 'Preventive Maintenance', name: 'Preventive Maintenance' },
 { id: 'Corrective Maintenance', name: 'Corrective Maintenance' },
 { id: 'Calibration', name: 'Calibration' },
]);


const createData = async() => {
    const result = await swal.fire({
            title: "Are you sure?",
            
            icon: "warning",
            showCancelButton: true,
        });
        if (result.isConfirmed) {
            axios
        .post("/web/job-request", form)
        .then((response) => {
            toastr.success(response.data.message);
        })
        .catch((error) => {
            toastr.error(error.data.message);
        });
        }

};
//part index



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

                    />


                    <FormSelectOption
                        name="branch"
                        label="Area / Department"
                        v-model="form.site_id"
                        :options="listsite"

                    />


                    <FormTextField
                        label="End User"
                        placeholder="End User"
                        name="enduser"
                        type="text"
                        id="enduser"
                        v-model="form.end_user"

                    />

                    <FormTextField
                        label="Time Requested"
                        placeholder="Time Requested"
                        name="time_requested"
                        type="date"
                        id="time_requested"
                        v-model="form.time_requested"

                    />

                    <FormTextField
                        label="Date needed"
                        placeholder="Date needed"
                        name="date_needed"
                        type="date"
                        id="date_needed"
                        v-model="form.date_needed"

                    />
                    <FormTextField
                        label="Noted By"
                        placeholder="Noted By"
                        name="noted_by"
                        type="text"
                        id="noted_by"
                        v-model="form.noted_by"

                    />
                    <FormSelectOption
                        name="type_of_job"
                        label="Type of Job"
                        v-model="form.type_of_job"
                        :options="listjobs"

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

                    />
                </tab-content>
                <tab-content title="Replacement Parts" icon="fas fa-tools">
                    <div class="form-group row">
                        <label
                            for="replacementParts"
                            class="col-sm-2 col-form-label"
                            >Replacement Parts   <button type="button" class="btn btn-warning btn-xs" @click="addPart"><i class="fa fa-plus"></i></button>  </label
                        >

                        <div class="col-sm-10">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Part Number</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(part, index) in form.replacement_parts" :key="index">
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Description"
                                                 v-model="part.description"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Part Number"
                                                 v-model="part.part_number"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                class="form-control"
                                                placeholder="Quantity"
                                                 v-model="part.quantity"
                                            />
                                        </td>
                                        <td>
                                         <button type="button" class="btn btn-primary" @click="removePart(index)"><i class="fa fa-trash"></i></button>
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
