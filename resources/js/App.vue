<!-- App.vue -->

<template>
    <div>
        <MainLayout v-if="!is404 && !isTechRecomApprovePage && !isJobOrderRequestApproved && !isMrfApproved && !isPrintAsset" />
        <TechRecommApproved v-else-if="isTechRecomApprovePage" />
        <JobOrderRequestApproved v-else-if="isJobOrderRequestApproved" />
        <MrfApproved v-else-if="isMrfApproved" />
        <PrintAsset v-else-if="isPrintAsset" />
        <ErrorLayout v-else />
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthUserStore } from "./stores/AuthUserStore";
import { useRoute } from "vue-router"; // Import useRoute function
const authUserStore = useAuthUserStore();
const route = useRoute(); // Access the current route
import MainLayout from "./CustomMainLayout.vue"; // Import the CustomMainLayout component
import ErrorLayout from "./404.vue"; // Import the ErrorLayout component
import TechRecommApproved from "./pages/techrecomm/TechRecommApproved.vue";
import JobOrderRequestApproved from "./pages/joborder/JobOrderRequestApproved.vue";
import MrfApproved from "./pages/Mrf/MrfRequestApproved.vue";
import PrintAsset from "./pages/assetmonitoring/PrintAsset.vue";
// Check if the current route is the TechRecomApprove page
const isTechRecomApprovePage = computed(() => {
    return route.name === "Tech-Approved"; // Adjust the route name as per your route configuration
});

const isJobOrderRequestApproved = computed(() =>{
    return route.name === "Job-Order-Request-Approved";
});

const isMrfApproved = computed(() =>{
    return route.name === "marial-Requisition-Approved";
});

const isPrintAsset = computed(() =>{
    return route.name === "print-asset";
});

const is404 = computed(() => {
    return route.name === "404 Error Page";
});
</script>

<style scoped>
/* Add your styling for the main App.vue layout if needed */
</style>
