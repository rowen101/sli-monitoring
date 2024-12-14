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


const pageTitle = `${useRoute().name}`;
const toastr = useToastr();
const isloading = ref(false);

const formValues = ref();

const checked = ref(true);
const form = reactive({
    site_name: "",
    id: "",
    site_id: "",
    asset_name: "",
    asset_type: "",
    serial: "",
    date_acquired: "",
    man_supplier: "",
    unit: "",
    location: "",
    paccountable: "",
    locationchangetranfer: "",
    cucodition: "",
    maintenancenotes: "",
    purchasecost: "",
    depreciationcost: "",
    Depreciationcostbyyear: "",
    is_active: "",
});

const selectedStatus = ref(null);

const listsite = ref();

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
    <Form
        @submit="handleSubmit"
        :validation-schema="editing ? editUserSchema : createUserSchema"
        v-slot="{ errors }"
        :initial-values="formValues"
    >
        <div class="col-md-12">
            <form-wizard
                @on-complete="createData"
                color="#094899"
                step-size="xs"
            >
                <tab-content title="Job Information" icon="fas fa-book">
                    <div class="form-group row">
                        <label for="jobOrderNumber" class="col-sm-2 col-form-label">Job Order Number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="jobOrderNumber" placeholder="Job Order Number">
                        </div>
                    </div>

                    <div class="form-group row">
                                <label for="branch"  class="col-sm-2 col-form-label">Area / Department</label>
                                <div class="col-sm-10">

                                <Field
                                    as="select"
                                    name="branch"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errors.branch,
                                    }"
                                    id="branch"
                                    aria-describedby="branchHelp"
                                    v-model="form.site_id"
                                >
                                    <option value="" disabled>
                                        Select Site
                                    </option>

                                    <option
                                        v-for="site in listsite"
                                        :key="site.id"
                                        :value="site.id"
                                    >
                                        {{ site.site_name }}
                                    </option>
                                </Field>
                                </div>
                                <span class="invalid-feedback">{{
                                    errors.branch
                                }}</span>
                            </div>
                            <div class="form-group row">
            <label for="endUser" class="col-sm-2 col-form-label">End User</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="endUser" placeholder="End User">
            </div>
        </div>
        <div class="form-group row">
            <label for="timeRequested" class="col-sm-2 col-form-label">Time Requested</label>
            <div class="col-sm-10">
                <input type="datetime-local" class="form-control" id="timeRequested" placeholder="Time Requested">
            </div>
        </div>
        <div class="form-group row">
            <label for="dateNeeded" class="col-sm-2 col-form-label">Date Needed</label>
            <div class="col-sm-10">
                <input type="date" class="form-control" id="dateNeeded" placeholder="Date Needed">
            </div>
        </div>
        <div class="form-group row">
            <label for="notedBy" class="col-sm-2 col-form-label">Noted By</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="notedBy" placeholder="Noted By">
            </div>
        </div>
        <div class="form-group row">
            <label for="typeOfJob" class="col-sm-2 col-form-label">Type of Job</label>
            <div class="col-sm-10">
                <select class="form-control" id="typeOfJob">
                    <option>Preventive Maintenance</option>
                    <option>Corrective Maintenance</option>
                    <option>Calibration</option>
                </select>
            </div>
        </div>


                </tab-content>
                <tab-content title="Problem Details" icon="fas fa-pen-alt">
                    <div class="form-group row">
            <label for="problemDescription" class="col-sm-2 col-form-label">Problem Description</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="problemDescription" rows="3" placeholder="Problem Description"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label for="Recommendations" class="col-sm-2 col-form-label">Recommendations</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="Recommendations" rows="3" placeholder="Recommendations"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label for="commitmentDate" class="col-sm-2 col-form-label">Commitment Date</label>
            <div class="col-sm-10">
                <input type="date" class="form-control" id="commitmentDate" placeholder="Commitment Date">
            </div>
        </div>

                </tab-content>
                <tab-content title="Replacement Parts" icon="fas fa-tools">
                    <div class="form-group row">
            <label for="replacementParts" class="col-sm-2 col-form-label">Replacement Parts</label>
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
                        <td><input type="text" class="form-control" placeholder="Description"></td>
                        <td><input type="text" class="form-control" placeholder="Part Number"></td>
                        <td><input type="number" class="form-control" placeholder="Quantity"></td>
                    </tr>
                    <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>


                </tab-content>
            </form-wizard>
        </div>
    </Form>
</template>


