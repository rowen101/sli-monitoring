<template>
    <div id="exportPDF">
        <br />
        <div class="container">
            <div class="row align-items-center">
                <div class="container-fluid">
                    
                    <div class="row">
        <div class="col-auto">
            <div v-if="lists.data.length > 0">
                <div class="asset-tag" v-for="(item, index) in lists.data" :item=item :key="index">
                    <div class="d-flex align-items-center">
                        <div class="qr-code">
                            <!-- QR Code Component -->
            
                            <QRCodeVue3
                                :width="200"
                                :height="200"
                                :value="generateQRCodeValue(item)"
                                :qr-options="{
                                errorCorrectionLevel: 'H'
                                }"
                                :image-options="{ hideBackgroundDots: true, imageSize: 1.5, margin: 10 }"
                                :corners-square-options="{ type: 'dot', color: '#34495E' }"
                                :corners-dot-options="{ type: undefined,color: '#41B883'}"
                                :dots-options="{ type: 'dots', color: '#41B883',
                                gradient: {
                                    type: 'linear',
                                    rotation: 0,
                                    colorStops: [
                                    { offset: 0, color: '#41B883' },
                                    { offset: 1, color: '#34495E' }
                                    ]
                                }
                                }"
                                :image="'/img/logo.png'"
                                :download="false"
                            />
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

// Function to generate QR code value based on item data
const generateQRCodeValue = (item) => {
    // Customize this to generate a unique value for the QR code
    return JSON.stringify({
        serial: item.serial,
        assetName: item.asset_name,
        dateAcquired: item.date_acquired,
        purchaseCost: item.purchasecost,
        accountable: item.paccountable
    });
};

// Function to fetch bulk print data and update the lists
const ActionBulkPrint = () => {
    axios
        .get(`/web/asset/bulkprint/${numericIdsArray.join(',')}`)
        .then((response) => {
            if (Array.isArray(response.data)) {
                // Map through the response data and add a QR code value for each item
                lists.value.data = response.data.map(item => ({
                    ...item,
                    qrCodeValue: generateQRCodeValue(item) // Add a qrCodeValue property
                }));
            } else {
                console.error('Expected an array but got:', response.data);
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
    width: auto;
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
