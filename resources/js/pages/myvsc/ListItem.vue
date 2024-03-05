<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";
import axios from "axios";
import moment from 'moment';
import { useAuthUserStore } from "../../stores/AuthUserStore";
import DOMPurify from 'dompurify';
const toastr = useToastr();
const authUserStore = useAuthUserStore();
const props = defineProps({
    item: Object,
    index: Number,
    selectAll: Boolean,
});

// const emit = defineEmits(["showItem"]);

// const toggleSelection = () => {
//     emit("toggleSelection", props.item);
// };




</script>
<template>
    <tr>
        <td>{{moment(item.taskdate).format('MMMM D, YYYY')}}</td>
        <td class="text-center" ><span v-if="item.task_lists_count !== 0">{{ item.task_lists_count }}</span></td>
        <td class="text-center"><span v-if="item.completed_task_count !== 0">{{ item.completed_task_count }}</span></td>
        <td class="text-center"><span v-if="item.status !== 'On Going'">{{  item.status }}</span></td>
    <td v-if="item.remarks === 'HIT'" class="bg-success text-center">{{ item.remarks }}</td>
<td v-else-if="item.remarks === 'MISS'" class="bg-danger text-center">{{ item.remarks }}</td>
<td v-else-if="item.remarks === null" class="text-center">{{item.startdate != null ? 'On Going!':''}}</td>


         <td :class="{
        'bg-danger': item.percentage_completed >= 0 && item.percentage_completed <= 59,
        'bg-orange': item.percentage_completed > 60 && item.percentage_completed <= 90,
        'bg-success': item.percentage_completed === 100,
        'text-center': true
        }">
        {{ item.percentage_completed + '%'}}
        </td>
    </tr>
</template>

