<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";
import axios from "axios";

const isModaltrue =ref(null)
const props = defineProps({
    item: Object,
    index: Number,

});


</script>
<template>

</template>
<style scoped>


</style>
