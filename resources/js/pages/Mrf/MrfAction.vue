<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch ,inject} from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import { debounce } from "lodash";
import * as yup from "yup";
import { useToastr } from "@/toastr.js";
import ContentLoader from "@/components/ContentLoader.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import { useRoute, useRouter } from "vue-router";
import FormTextField from "@/components/FormTextField.vue";
import FormSelectOption from "@/components/FormSelectOption.vue";
import FormTextArea from "@/components/FormTextAria.vue";

const route = useRoute(); // Get current route details
const router = useRouter();

const isLoading = ref(false);
const pageTitle = `${route.name}`;
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
    mrf_order_number: "",
    requisitioner: "",
    date_requested: "",
    date_needed: "",
    purpose: "",
    status: "P",
    Item_request: [
    {

        particulars: '',
        description: '',
        quantity: 0,
        uom: '',
        unit_price: 0,
        total_amount: 0,
    },]

});

const addPart = ()=>{
    form.Item_request.push({
        particulars: '',
        description: '',
        quantity: 0,
        uom: '',
        unit_price: 0,
        total_amount: 0,
    });

}

const removePart =(index)=>{
    if(form.Item_request.length >1){
    form.Item_request.splice(index, 1);
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




const createData = async() => {
    const result = await swal.fire({
            title: "Are you sure?",
            icon: "warning",
            showCancelButton: true,
        });
        if (result.isConfirmed) {
            isLoading.value = true;
            axios
        .post("/web/Mrf-request", form)
        .then((response) => {
            if (response.status === 200) { // Check if the response status is OK
                    toastr.success(response.data.message);
                    router.push("/Marial-Requisition");
                } else {
                    toastr.error("Something went wrong. Please try again.");
                }
        })
        .catch((error) => {
            toastr.error(error.data.message);
        })
        .finally(() => {
            isLoading.value = false;
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
               <!-- Loading animation -->
    <div v-if="isLoading" class="spinner">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden"></span>
      </div>
    </div>
            <form-wizard
                @on-complete="createData"
                color="#094899"
                step-size="xs"
            >
                <tab-content title="Requisition Info" icon="fas fa-book">

                    <FormTextField
                        label="Material Requisition Number"
                        placeholder="Material Requisition Number - AutoGenerate"
                        name="mrf_order_number"
                        type="text"
                        id="mrf_order_number"
                        v-model="form.mrf_order_number"

                    />


                    <FormSelectOption
                        name="branch"
                        label="Area / Department"
                        v-model="form.site_id"
                        :options="listsite"

                    />


                    <FormTextField
                        label="Requisitioner"
                        placeholder="Requisitioner"
                        name="requisitioner"
                        type="text"
                        id="requisitioner"
                        v-model="form.requisitioner"

                    />

                    <FormTextField
                        label="Date Requested"
                        placeholder="Date Requested"
                        name="date_requested"
                        type="date"
                        id="date_requested"
                        v-model="form.date_requested"

                    />

                    <FormTextField
                        label="Date needed"
                        placeholder="Date needed"
                        name="date_needed"
                        type="date"
                        id="date_needed"
                        v-model="form.date_needed"

                    />
                    <FormTextArea
                        label="Purpose"
                        placeholder="Purpose"
                        name="purpose"
                        type="text"
                        id="purpose"
                        row="3"
                        v-model="form.purpose"

                    />
                   

                </tab-content>

                <tab-content title="List of Material" icon="fas fa-tools">
                    <div class="form-group row">
                        <label
                            for="replacementParts"
                            class="col-sm-2 col-form-label"
                            >List of Material   <button type="button" class="btn btn-warning btn-xs" @click="addPart"><i class="fa fa-plus"></i></button>  </label
                        >

                        <div class="col-sm-10">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Particular</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>UOM</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(part, index) in form.Item_request" :key="index">
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                 v-model="part.particulars"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                 v-model="part.description"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                class="form-control"
                                                 v-model="part.quantity"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                 v-model="part.uom"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                class="form-control"
                                                 v-model="part.unit_price"
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

<style scoped>
.spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
}
</style>
