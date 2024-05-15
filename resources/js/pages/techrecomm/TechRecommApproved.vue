<template>
    <div id="exportPDF">

        <div class="container">
             <br>
            <div class="header">
                <img
                    class="logo"
                    :src="'/img/IT_Technical_Recommendation .jpg'"
                    alt="Right Logo"
                />

                <img
                    class="logo"
                    :src="'/img/safexpress.jpeg'"
                    alt="Left Logo"
                />
            </div>
            <div class="header">
                <span
                    >Recommendation No:
                    <strong>{{ form.recommnum }}</strong></span
                >
                <span
                    >Date Checked: <strong>{{ form.created_at }}</strong></span
                >
            </div>
            <div class="header-justify-space-between">
                <div>
                    <span
                        >Company: <strong>{{ form.company }}</strong></span
                    ><br />
                    <span
                        >Branch: <strong>{{ form.branch }}</strong></span
                    >
                </div>

                <p></p>
                <div>
                    <span v-if="form.warehouse == ''"
                        >Warehouse: <strong></strong>{{ form.warehouse }}</span
                    ><br />
                    <span
                        >User: <strong>{{ form.user }}</strong></span
                    ><br />
                    <span
                        >Department:
                        <strong>{{ form.department }}</strong></span
                    >
                </div>

                <p></p>
            </div>

            <strong>Unit details</strong>
            <div class="content">
                <div class="header-justify-space-between">
                    <div>
                        <span>Brand:</span><strong>{{ form.brand }}</strong
                        ><br />
                        <span>Model:</span><strong>{{ form.model }}</strong
                        ><br />
                    </div>
                    <div></div>
                    <div>
                        <span>Serial #:</span
                        ><strong>{{ form.serialnum }}</strong
                        ><br />
                        <span>Asset Tag:</span
                        ><strong>{{ form.assettag }}</strong
                        ><br />
                    </div>
                    <div></div>
                </div>
            </div>
            <strong>Reported problem</strong>
            <div class="content">
                <p>
                    {{ form.problem }}
                </p>
            </div>

            <strong>Assessment conducted</strong>
            <div class="content">
                <p>
                    {{ form.assconducted }}
                </p>
            </div>

            <strong>Recommendation</strong>
            <div class="content">
                <p>
                    {{ form.recommendation }}
                </p>
            </div>
            <div class="header-justify-space-between">
                <div>
                    <strong>Evaluated by:</strong><br />
                    <span class="botoom-line">{{ form.createdby }}</span
                    ><br />
                    <span>{{form.created_position}}</span>

                </div>

                <p></p>
                <div>
                    <strong>Noted by:</strong><br />
                    <span class="botoom-line">{{ form.updatedby }}</span
                    ><br />
                    <span>{{form.updated_position}}</span><br />
                </div>

                <p></p>
            </div>

            <span v-if="form.status === 2"
                >Approved date:({{ form.updated_at }})</span
            >
            <span v-else>Pending</span>
        </div>
    </div>

    <div class="fab-container " v-if="form.status === 2">
        <div class="button iconbutton bg-success"  @click="exportPDF()">

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
    recommnum: "",
    branch: "",
    company: "",
    department: "",
    warehouse: "",
    user: "",
    problem: "",
    brand: "",
    model: "",
    status: "",
    assettag: "",
    serialnum: "",
    assconducted: "",
    recommendation: "",
    created_at: "",
    updated_at: "",
    createdby: "",
    updatedby: "",
    created_position:"",
    updated_position: "",
});

const getechrecom = () => {
    axios
        .get(`/api/tech-recommendations/${useRoute().params.id}`)
        .then((response) => {
            (form.id = response.data.id),
                (form.recommnum = response.data.recommnum),
                (form.branch = response.data.branch),
                (form.company = response.data.company),
                (form.department = response.data.department);
            form.warehouse = response.data.warehouse;
            form.user = response.data.user;
            form.problem = response.data.problem;
            form.brand = response.data.brand;
            form.model = response.data.model;
            form.assettag = response.data.assettag;
            form.serialnum = response.data.serialnum;
            form.status = response.data.status;
            form.assconducted = response.data.assconducted;
            form.recommendation = response.data.recommendation;
            form.createdby = response.data.createdby.cfull_name;
            form.updatedby = response.data.updatedby.ufull_name;
            form.created_at = moment(response.data.created_at).format(
                "MMMM D, YYYY"
            );
            form.updated_at = moment(response.data.updated_at).format(
                 "MMMM D, YYYY [at] h:mm:ss a"
            );
form.created_position = response.data.createdby.cposition;
form.updated_position = response.data.updatedby.uposition;
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        });
};
onMounted(() => {
    getechrecom();

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
.header,
.header-justify-space-between {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.header-justify-space-between {
    margin-bottom: 10px;
    align-items: left;
}
.logo {
    width: 260px; /* Adjust size as needed */
    height: auto;
}
.botoom-line {
    border-bottom: 1px solid #757575;
}
.content {
    line-height: 1.6;
    border: 2px solid #757575;
    margin-bottom: 20px;
}
.signature {
    margin-top: 40px;
}
</style>
