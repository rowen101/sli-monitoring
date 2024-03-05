<!-- App.vue -->

<template>
    <div>
        <MainLayout v-if="!is404 && !isTechRecomApprovePage" />
        <TechRecomApprove v-else-if="isTechRecomApprovePage" />
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
import TechRecomApprove from "./pages/techrecomm/TechRecommApprove.vue";

// Check if the current route is the TechRecomApprove page
const isTechRecomApprovePage = computed(() => {
    return route.name === "Tech-Approved"; // Adjust the route name as per your route configuration
});

const is404 = computed(() => {
    return route.name === "404 Error Page";
});
</script>

<style scoped>
/* Add your styling for the main App.vue layout if needed */
</style>
