<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";
import axios from "axios";
import { useAuthUserStore } from "../../stores/AuthUserStore";

import moment from 'moment';
const toastr = useToastr();
const authUserStore = useAuthUserStore();
const props = defineProps({
    item: Object,
    index: Number,
});

</script>
<template>
    <tr>
         <td>{{ item.sitename }}</td>
        <td>{{moment(item.taskdate).format('MMMM D, YYYY')}}</td>
        <td>{{ item.startdate }}</td>
        <td>{{ item.enddate }}</td>
        <td>{{ item.tasktype.listtask }}</td>
        <td>{{ item.status }}</td>
        <td class="text-center"><span :class="[ 'badge', item.remarks === 'HIT' ? 'bg-success' : 'bg-danger' ]">
    {{ item.remarks }}</span></td>

    </tr>
</template>

<style scoped>
.custom-link {
  color: black; /* or any other color you prefer */
  text-decoration: none; /* Optional: Remove underline */
}
</style>
