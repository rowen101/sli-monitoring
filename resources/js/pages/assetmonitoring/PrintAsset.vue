<template>
    <div id="exportPDF">
        <br />
        <div class="container">
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
                            <span class="m-0 font-weight-bold">Print Asset Tag</span>
                        </div>
                        <div class="col-sm font-weight-bold font-italic"></div>
                    </div>
                    <div class="row">
        <div class="col-auto">
            <div v-if="lists.data.length > 0">
                <div class="asset-tag" v-for="(item, index) in lists.data" :key="item.id">
                    <div class="d-flex align-items-center">
                        <div class="qr-code">
                            <!-- QR Code Component -->
                            <!-- <QRCodeVue3
                                value="https://safexpress.com.ph/"
                                :width="100"
                                :height="100"
                                imgclass="img-qr"
                                :backgroundOptions="{ color: '#ffffff' }"
                                :cornersSquareOptions="{ type: 'dot', color: '#000000' }"
                                :cornersDotOptions="{ type: undefined, color: '#000000' }"
                            /> -->
                        </div>
                        <div class="content ml-3">
                            <div>Property Owner</div>
                            <div>Safexpress Logistics Inc.</div>
                            <div>Purchase Cost: {{ item.purchasecost }}</div>
                            <div>Accountable: {{ item.paccountable }}</div>
                            <div>S/N {{ item.serial }}</div>
                            <div>Asset Name: {{ item.asset_name }}</div>
                            <div>Date Acquired: {{ item.date_acquired }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <p>No data available.</p>
            </div>
        </div>
    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="fab-container">
        <div class="button iconbutton bg-success" @click="exportPDF()">
            <i class="fas fa-download"></i>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router"; 
import moment from "moment";
import html2pdf from "html2pdf.js";
import QRCodeVue3 from "qrcode-vue3";


const route = useRoute(); // Get the current route
const router = useRouter(); 


const lists = ref({ data: [] });
const selectedItems = sessionStorage.getItem('selectedItems');
const selectedIdsArray = selectedItems
    ? selectedItems
        .split(',') // Split into an array
        .map(item => item.trim()) // Trim whitespace from each item
        .filter(item => item !== '') // Remove empty strings
    : [];
const numericIdsArray = selectedIdsArray.map(Number);

sessionStorage.removeItem('selectedIdsArray');

const pageTitle = "Print Asset Tag";;
const exportPDF = () => {
    const element = document.getElementById("exportPDF"); // Specify the element to convert to PDF
    html2pdf().from(element).save(`${pageTitle}.pdf`);
};
const formatPrice = (value) => {
      return value ? value.toFixed(2) : '0.00'; // Format to 2 decimal places
    };


const ActionBulkPrint = () => {
    axios
    .get(`/web/asset/bulkprint/${numericIdsArray.join(',')}`)
        .then((response) => {
            if (Array.isArray(response.data.record)) {
                lists.value.data = response.data.record;
            } else {
                console.error('Expected an array but got:', response.data.record);
            }
        })
        .catch((error) => {
            console.log(error.message);
        });
};

onMounted(() => {
    ActionBulkPrint();
    
    document.title = pageTitle;
});
</script>

<style scoped>
body {
    font-family: Arial, sans-serif;

}
.container {
    position: relative;
    width: 800px;
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


.asset-tag {
            text-align: left;
            font-family: Arial, sans-serif;
            padding: 10px;
            border: 1px solid #4b4646;
            display: inline-block;
            margin: 20px;
        }

        .qr-code {
    margin-right: 20px; /* Space between QR code and content */
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
