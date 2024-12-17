<template>
    <div id="exportPDF">
        <div class="container">
            <br />
            <div class="header">
                <img class="logo" :src="'/img/logo.png'" alt="Left Logo" />
                 
            </div><h2 class="center ">JOB ORDER REQUEST FORM</h2>
            <div class="row border p-2 ">
                <div class="col-6 float-end">
                    <p class="mb-1">
                        <strong>Job Order Number :</strong
                        >{{ form.jobOrderNumber }}
                    </p>
                </div>
            </div>

            <!-- Details Section -->
        
                <div class="row border">
                    <div class="col-3 border p-2" style="color:#525659">
                        Area/Department<br />
                        <div class="mt-2">{{ form.areaDepartment }}</div>
                    </div>
                    <div class="col-3 border p-2">
                        End User<br />
                        <div class="mt-2">{{ form.endUser }}</div>
                    </div>
                    <div class="col-3 border p-2">
                        Date/Time Requested<br />
                        <div class=" mt-2">{{ form.dateTimeRequested }}</div>
                    </div>
                    <div class="col-3 border p-2">
                        Date Needed<br />
                        <div class="mt-2">{{ form.dateNeeded }}</div>
                    </div>
                </div>
            
            <div class="row border p-2">
                <div class="col-3 border p-2">Noted by: <b>{{ form.notedBy }}</b></div>
            </div>

            <!-- Type of Job Done -->
            <div class="row mt-2 border">
                <div class="col-12 p-2">
                    <span class="section-title">Type of Job Done</span><br />
                    ( &nbsp; ) Preventive Maintenance &nbsp; ( &nbsp; )
                    Corrective Maintenance &nbsp; ( &nbsp; ) Calibration
                </div>
            </div>

            <!-- Problem Description -->
            <div class="row border mt-2">
                <div class="col-12 p-2">
                    <span class="section-title">Problem Description/s:</span>
                    <div class="border-box mt-2"></div>
                </div>
            </div>

            <!-- Findings -->
            <div class="row border mt-2">
                <div class="col-12 p-2">
                    <span class="section-title"
                        >Findings and Recommendations:</span
                    >
                    <div class="border-box mt-2"></div>
                </div>
            </div>

            <!-- Commitment Date -->
            <div class="row border mt-2">
                <div class="col-6 p-2">Commitment Date:</div>
                <div class="col-6 text-right p-2">
                    <small
                        >Note: Pls notify requestor for the schedule
                        given</small
                    >
                </div>
            </div>

            <!-- Replacement Parts Needed Table -->
            <div class="row border mt-2">
                <div class="col-12 p-2">
                    <span class="section-title">Replacement Parts Needed</span>
                    <table
                        class="table table-bordered text-center mb-0"
                        id="replacementPartsTable"
                    >
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Description</th>
                                <th>Part No. (If applicable)</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(part, index) in form.replacementParts"
                                :key="index"
                            >
                                <td>{{ index + 1 }}</td>
                                <td>{{ part.description }}</td>
                                <td>{{ part.part_number }}</td>
                                <td>{{ part.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="row border mt-2">
                <div class="col-6 p-2">Checked by :</div>
                <div class="col-3 p-2">Date:</div>
                <div class="col-3 p-2">Time:</div>
            </div>

            <div class="row mt-2 border">
                <div class="col-12 text-center p-2">
                    <span class="section-title">REVIEW AND APPROVAL</span>
                </div>
            </div>
            <!-- Remarks Section -->
            <div class="row border mt-2">
                <div class="col-12 p-2">
                    Remarks / Comments:
                    <div class="border-box mt-2"></div>
                </div>
            </div>
            <div class="row border">
                <div class="col-6 p-2">
                    Reviewed by: Department Manager<br />
                    <div class="border-box mt-2"></div>
                </div>
                <div class="col-6 p-2">
                    Approved by: President/CEO<br />
                    <div class="border-box mt-2"></div>
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

const form = reactive({
    id: "",
    jobOrderNumber: "",
    areaDepartment: "",
    endUser: "",
    dateTimeRequested: "",
    dateNeeded: "",
    notedBy: "",
    preventiveMaintenance: false,
    correctiveMaintenance: false,
    calibration: false,
    problemDescription: "",
    findingsRecommendations: "",
    commitmentDate: "",
    replacementParts: [],
    checkedBy: "",
    checkedDate: "",
    checkedTime: "",
    reviewedBy: "",
    reviewedDate: "",
    approvedBy: "",
    approvedDate: "",
    remarks: "",
    status: "",
});

const getJobOrder = () => {
    axios
        .get(`/web/job-request/${useRoute().params.id}`)
        .then((response) => {
            form.id = response.data.record.id;
            form.jobOrderNumber = response.data.record.job_order_number;
            form.areaDepartment = response.data.record.site_name;
            form.endUser = response.data.record.end_user;
            form.dateTimeRequested = moment(response.data.record.time_requested).format(
                "MMMM D, YYYY"
            );
            form.dateNeeded = moment(response.data.record.date_needed).format(
                "MMMM D, YYYY"
            );
        
            form.notedBy = response.data.record.noted_by;
            form.preventiveMaintenance =
                response.data.record.preventiveMaintenance;
            form.correctiveMaintenance =
                response.data.record.correctiveMaintenance;
            form.calibration = response.data.record.calibration;
            form.problemDescription = response.data.record.problemDescription;
            form.findingsRecommendations =
                response.data.record.findingsRecommendations;
            form.commitmentDate = response.data.record.commitmentDate;
            form.replacementParts = response.data.record.replacement_parts;
            form.checkedBy = response.data.record.checkedBy;
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
            form.approvedBy = response.data.record.approvedBy;
            form.approvedDate = moment(
                response.data.record.approvedDate
            ).format("MMMM D, YYYY");
            form.remarks = response.data.record.remarks;
            form.status = response.data.record.status;
        })
        .catch((error) => {
            actions.setErrors(error.response.data.record.error);
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
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    border-left: 1px solid #ccc;
    border-right: 1px solid #ccc;
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
</style>
