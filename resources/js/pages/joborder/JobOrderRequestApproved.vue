<template>
    <div id="exportPDF">
        <br />
        <div class="container">
            <div
                v-if="form.status !== 'A'"
                :class="[
                    'ribbon',
                    {
                        warning: form.status === 'P',
                        closed: form.status === 'C',
                    },
                ]"
            >
                <span>{{
                    form.status === "P"
                        ? "Pending"
                        : form.status === "C"
                        ? "Closed"
                        : form.status
                }}</span>
            </div>
            <div class="row align-items-center">
                <!-- Left Logo Section -->
                <div class="col-3 p-2 text-center">
                    <div class="header">
                        <img
                            class="logo img-fluid"
                            :src="'/img/logo.png'"
                            alt="Left Logo"
                        />
                    </div>
                </div>

                <!-- Center Title Section -->
                <div class="col-9 p-2 text-center">
                    <h3 class="m-0 font-weight-bold">JOB ORDER REQUEST FORM</h3>
                </div>
            </div>

            <div class="row border border-secondary p-2">
                <div class="col-6 float-end">
                    <p class="mb-1">
                        <strong>Job Order Number :</strong
                        >{{ form.jobOrderNumber }}
                    </p>
                </div>
            </div>

            <!-- Details Section -->

            <div class="row">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                        <th>Area/Department</th>
                        <th>End User</th>
                        <th>Date/Time Requested</th>
                        <th>Date Needed</th>
                    </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>{{ form.areaDepartment }}</td>
                            <td>{{ form.endUser }}</td>
                            <td>{{ form.dateTimeRequested }}</td>
                            <td>{{ form.dateNeeded }}</td>
                        </tr>
                    </tbody>
                </table>


            </div>
            <div class="row border">
                <div class="col-6 p-2">Noted by:<b>{{ form.notedBy }}</b> </div>

            </div>


            <!-- Type of Job Done -->
            <div class="row">
                <div class="col-12 p-2">
    <span class="section-title">Type of Job Done</span><br />
    <span>{{ form.typeofjob === 'Preventive Maintenance' ? '(&check;)' : '( &nbsp; )' }} Preventive Maintenance</span>&nbsp;
    <span>{{ form.typeofjob === 'Corrective Maintenance' ? '(&check;)' : '( &nbsp; )' }} Corrective Maintenance</span>&nbsp;
    <span>{{ form.typeofjob === 'Calibration' ? '(&check;)' : '( &nbsp; )' }} Calibration</span>
    </div>

            </div>

            <!-- Problem Description -->
            <div class="row ">
                <div class="col-12 p-2">
                    <span class="section-title">Problem Description/s:</span>

                        <div class="row justify-content-right">
                            <div class="col-12 hidden-md-down border border-secondary">
                                <p>
                                    {{ form.problemDescription }}


                                </p>
                            </div>
                        </div>

                </div>
            </div>
            <div class="row ">
                <div class="col-12 text-center p-2">
                    <span class="section-title">To Be Filled By Maintenance</span>
                </div>
            </div>
            <!-- Findings -->
            <div class="row">
                <div class="col-12 p-2">
                    <span class="section-title"
                        >Findings and Recommendations:</span
                    >
                    <div class="row justify-content-right">
                            <div class="col-12 hidden-md-down border border-secondary">
                                <p>
                                    {{ form.findingsRecommendations }}


                                </p>
                            </div>
                        </div>

                </div>
            </div>

            <!-- Commitment Date -->
            <div class="row border mt-2">
                <div class="col-6 p-2">Commitment Date:&nbsp;<b>{{ form.commitmentDate }}</b></div>
                <div class="col-6 text-right p-2">
                    <small
                        >Note: Pls notify requestor for the schedule
                        given</small
                    >
                </div>
            </div>

            <!-- Replacement Parts Needed Table -->
            <div class="row">

                    <span class="section-title">Replacement Parts Needed</span>
                    <table class="table table-bordered table-sm">
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

            <!-- Footer Section -->
            <div class="row border border-secondary mt-2">
                <div class="col-6 p-2">
                    Checked by : <b>{{ form.checkedBy }}</b>
                </div>
                <div class="col-3 p-2">Date:</div>
                <div class="col-3 p-2">Time:</div>
            </div>

            <div class="row">
                <div class="col-12 text-center p-2">
                    <span class="section-title">REVIEW AND APPROVAL</span>
                </div>
            </div>
            <!-- Remarks Section -->
            <div class="row">
                <div class="col-12 p-2">
                    Remarks / Comments:
                    <div class="row justify-content-right">
                            <div class="col-12 hidden-md-down border border-secondary">
                                <p>
                                    {{ form.findingsRecommendations }}

                                </p>
                            </div>
                        </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 p-2">
                    Reviewed by: Department Manager<br />
                    <div class=" mt-2">
                        <span class="bottom-line" style="border-bottom: 1px solid black; display: inline-block;">{{ form.approvedBy }}</span>
                    <br />
                    <span >{{form.approvedPosition}}</span>
                    </div>
                </div>
                <div class="col-6 p-2">
                    Approved by: President/CEO<br />
                    <div class=" mt-2">
                        <span class="bottom-line" style="border-bottom: 1px solid black; display: inline-block;">Eden Satinitigan</span>
                    </div>
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
    typeofjob: false,
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
    approvedPosition:""
});

const getJobOrder = () => {
    axios
        .get(`/web/job-request/${useRoute().params.id}`)
        .then((response) => {
            form.id = response.data.record.id;
            form.jobOrderNumber = response.data.record.job_order_number;
            form.areaDepartment = response.data.record.site_name;
            form.endUser = response.data.record.end_user;
            form.dateTimeRequested = moment(
                response.data.record.time_requested
            ).format("MMMM D, YYYY");
            form.dateNeeded = moment(response.data.record.date_needed).format(
                "MMMM D, YYYY"
            );

            form.notedBy = response.data.record.noted_by;
            form.typeofjob =
                response.data.record.type_of_job;

            form.calibration = response.data.record.calibration;
            form.problemDescription = response.data.record.problem_description;
            form.findingsRecommendations =
                response.data.record.findings_recommendations;
            form.commitmentDate = moment(response.data.record.commitment_date).format(
                "MMMM D, YYYY"
            );;
            form.replacementParts = response.data.record.replacement_parts;
            form.checkedBy = response.data.record.createdby.cfull_name;
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
            form.approvedPosition = response.data.record.updatedby.uposition ? response.data.record.updatedby.uposition : '';
            form.approvedDate = moment(
                response.data.record.approvedDate
            ).format("MMMM D, YYYY");
            form.remarks = response.data.record.remarks;
            form.status = response.data.record.status;
        })
        .catch((error) => {
            console.log(error)
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
    width: 720px;
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
</style>
