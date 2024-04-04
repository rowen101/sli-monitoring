<script setup>
import { ContentLoader } from "vue-content-loader";
</script>
<template>
    <ContentLoader v-if="isloading" viewBox="0 0 250 110">
        <rect x="0" y="0" rx="3" ry="3" width="250" height="10" />
        <rect x="0" y="20" rx="3" ry="3" width="250" height="10" />
        <rect x="0" y="40" rx="3" ry="3" width="250" height="10" />
        <rect x="0" y="60" rx="3" ry="3" width="250" height="10" />
    </ContentLoader>
</template>
