<template>
    <div id="exportPDF">
        <br />
        <div class="container">
            <div
                v-if="['P', 'C'].includes(form.Status)"
                :class="[
                    'ribbon',
                    {
                        warning: form.Status === 'P',
                        closed: form.Status === 'C',
                    },
                ]"
            >
                <span>{{
                    form.Status === "P"
                        ? "Pending"
                        : form.Status === "C"
                        ? "Reject"
                        : form.Status === "P"
                }}</span>
            </div>
            <div class="row align-items-center">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm">
                            <img
                                class="logo img-fluid"
                                :src="'/img/logo.png'"
                                alt="Left Logo"
                            />
                        </div>
                        <div class="col-sm">
                            <span class="m-0 font-weight-bold"
                                >MATERIALS REQUISITION FORM</span
                            >
                        </div>
                        <div class="col-sm font-weight-bold font-italic">
                            <span>{{ form.mrfOrderNumber }}</span>
                        </div>
                    </div>
                    <table class="table table-bordered table-sm ">
                        <tr>
                            <td>SITE: {{ form.areaDepartment }}</td>
                            <td>DATE REQUEST: {{ form.daterequested }}</td>
                        </tr>
                        <tr>
                            <td>REQUISITIONER: {{ form.checkedBy }}</td>
                            <td>DATE NEEDED: {{ form.dateNeeded }}</td>
                        </tr>
                    </table>
                    <table
                        class="table table-bordered text-center table-sm table-striped"
                    >
                        <thead class="bg-primary">
                            <tr>
                                <th>ITEM NO.</th>
                                <th>PARTICULARS</th>
                                <th>DESCRIPTION</th>
                                <th>QUANTITY</th>
                                <th>UNIT</th>
                                <th>UNIT PRICE</th>
                                <th>PRINCIPAL AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(part, index) in form.materiallist"
                                :key="index"
                            >
                                <td>{{ index + 1 }}</td>
                                <td>{{ part.particulars }}</td>
                                <td>{{ part.description }}</td>
                                <td>{{ part.quantity }}</td>
                                <td>{{ part.uom }}</td>
                                <td class="text-right">₱{{ part.unit_price }}</td>
                                <td class="text-right">₱{{ formatPrice(part.quantity * part.unit_price) }}</td>

                            </tr>
                        </tbody>
                    </table>
                    <p class="font-weight-bold">PURPOSE: {{ form.Purpose }}</p>

                    <table class="table table-bordered table-sm border-0">
                        <tr>
                            <td colspan="2">PREPARED BY: {{ form.checkedBy }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Note: Procurement cut-off time of receiving
                                requests: MWF @8AM-2PM
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                *7 Days lead for consumables (Repeat Order)
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                *10 Days lead time for (New Request) beyond TOR
                                for Local Vendor
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                *30 Days lead time for (New Request) beyond TOR
                                for Foreign Vendor
                            </td>
                        </tr>
                        <tr>
                            <td class="signature">
                               <span class="border-bottom border-dark">Prepared By: {{ form.checkedBy }}</span> <br />

                                {{ form.checkedPosition }}
                            </td>
                            <td class="signature">
                                Noted By:  {{ form.approvedBy }}<br />

                                {{ form.approvedPosition }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="fab-container" v-if="form.status === 'A'">
        <div class="button iconbutton bg-success" @click="exportPDF()">
            <i class="fas fa-download"></i>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
import moment from "moment";
import html2pdf from "html2pdf.js";

const pageTitle = `${useRoute().params.id}`;
const exportPDF = () => {
    const element = document.getElementById("exportPDF"); // Specify the element to convert to PDF
    html2pdf().from(element).save(`${pageTitle}.pdf`);
};
const formatPrice = (value) => {
      return value ? value.toFixed(2) : '0.00'; // Format to 2 decimal places
    };


const form = reactive({
    id: "",
    mrfOrderNumber: "",
    areaDepartment: "",
    requisitioner: "",
    daterequested: "",
    dateNeeded: "",
    Purpose: "",
    materiallist: [],
    checkedBy: "",
    checkedPosition: "",
    checkedDate: "",
    checkedTime: "",
    reviewedBy: "",
    reviewedDate: "",
    approvedBy: "",
    approvedDate: "",
    remarks: "",
    Status: "",
    approveby:"",
    approvedPosition: "",
});

const getJobOrder = () => {
    axios
        .get(`/web/Mrf-request/${useRoute().params.id}`)
        .then((response) => {
            form.id = response.data.record.id;
            form.mrfOrderNumber = response.data.record.mrf_order_number;
            form.areaDepartment = response.data.record.site_name;
            form.requisitioner = response.data.record.requisitioner;
            form.daterequested = moment(
                response.data.record.date_requested
            ).format("MMMM D, YYYY");
            form.dateNeeded = moment(response.data.record.date_needed).format(
                "MMMM D, YYYY"
            );

            form.Purpose = response.data.record.purpose;
            form.materiallist = response.data.record.mrf_items_parts;
            form.checkedBy = response.data.record.createdby.cfull_name;
            form.checkedPosition = response.data.record.createdby.cposition
                ? response.data.record.createdby.cposition
                : "";
            form.checkedDate = moment(response.data.record.checkedDate).format(
                "MMMM D, YYYY"
            );
            form.checkedTime = moment(response.data.record.checkedTime).format(
                "h:mm:ss a"
            );
            form.reviewedBy = response.data.record.reviewedBy;
            form.reviewedDate = moment(
                response.data.record.reviewedDate
            ).format("MMMM D, YYYY");
            form.approvedBy = response.data.record.updatedby.ufull_name;
            form.approvedPosition = response.data.record.updatedby.uposition
                ? response.data.record.updatedby.uposition
                : "";
            form.approvedDate = moment(
                response.data.record.approvedDate
            ).format("MMMM D, YYYY");
            form.remarks = response.data.record.remarks;
            form.Status = response.data.record.status;
            console.log(response.data.record.status);
        })
        .catch((error) => {
            console.log(error.message);
        });
};

onMounted(() => {
    getJobOrder();

    document.title = pageTitle;
});
</script>

<style scoped>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}
.container {
    position: relative;
    width: 60%;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
}
.table-bordered td,
.table-bordered th {
    border: 1px solid black !important;
}
.section-title {
    font-weight: bold;
    text-decoration: underline;
    margin-bottom: 5px;
}
.border-box {
    border: 1px solid black;
    height: 50px;
}
.logo {
    width: 260px; /* Adjust size as needed */
    height: auto;
}

/* ribbon */
.ribbon {
    position: absolute;
    right: -5px;
    top: -5px;
    z-index: 1;
    overflow: hidden;
    width: 93px;
    height: 93px;
    text-align: right;
}
.ribbon span {
    font-size: 0.8rem;
    color: #fff;
    text-transform: uppercase;
    text-align: center;
    font-weight: bold;
    line-height: 32px;
    transform: rotate(45deg);
    width: 125px;
    display: block;
    background: #79a70a;
    background: linear-gradient(#9bc90d 0%, #79a70a 100%);
    box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
    position: absolute;
    top: 17px;
    right: -29px;
}

.ribbon span::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 100%;
    z-index: -1;
    border-left: 3px solid #79a70a;
    border-right: 3px solid transparent;
    border-bottom: 3px solid transparent;
    border-top: 3px solid #79a70a;
}
.ribbon span::after {
    content: "";
    position: absolute;
    right: 0%;
    top: 100%;
    z-index: -1;
    border-right: 3px solid #79a70a;
    border-left: 3px solid transparent;
    border-bottom: 3px solid transparent;
    border-top: 3px solid #79a70a;
}

.closed span {
    background: linear-gradient(#f70505 0%, #8f0808 100%);
}
.closed span::before {
    border-left-color: #8f0808;
    border-top-color: #8f0808;
}
.closed span::after {
    border-right-color: #8f0808;
    border-top-color: #8f0808;
}

.warning span {
    background: linear-gradient(#febd1e 0%, #febd1e 100%);
}
.warning span::before {
    border-left-color: #febd1e;
    border-top-color: #febd1e;
}
.warning span::after {
    border-right-color: #febd1e;
    border-top-color: #febd1e;
}

.primary span {
    background: linear-gradient(#2989d8 0%, #1e5799 100%);
}
.primary span::before {
    border-left-color: #1e5799;
    border-top-color: #1e5799;
}
.primary span::after {
    border-right-color: #1e5799;
    border-top-color: #1e5799;
}
.foo {
    clear: both;
}

.bar {
    content: "";
    left: 0px;
    top: 100%;
    z-index: -1;
    border-left: 3px solid #79a70a;
    border-right: 3px solid transparent;
    border-bottom: 3px solid transparent;
    border-top: 3px solid #79a70a;
}

.baz {
    font-size: 1rem;
    color: #fff;
    text-transform: uppercase;
    text-align: center;
    font-weight: bold;
    line-height: 2em;
    transform: rotate(45deg);
    width: 100px;
    display: block;
    background: #79a70a;
    background: linear-gradient(#9bc90d 0%, #79a70a 100%);
    box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
    position: absolute;
    top: 100px;
    left: 1000px;
}
.red-box { background-color: red; padding: 10px; border: 1px solid #000; color: white; width: 100px; }
</style>
