<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "@/toastr.js";
import axios from "axios";
import { useAuthUserStore } from "@/stores/AuthUserStore.js";
import DOMPurify from 'dompurify';
const toastr = useToastr();
const authUserStore = useAuthUserStore();
const props = defineProps({
    item: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(["userDeleted", "editAction", "confirmDeletion"]);

const toggleSelection = () => {
    emit("toggleSelection", props.item);
};


</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index + 1 }}</td>
        <td>{{ item.site_name }}</td>
        <td>{{ item.job_order_number }}</td>
         <td>{{ item.end_user }}</td>
      <td>{{ item.date_needed }}</td>
       <td>{{ item.date_needed }}</td>
        <td>{{ item.commitment_date }}</td>
        <td style="text-align: center;">{{ item.status }}</td>
        <td>{{ item.created_at }}</td>
          <td class="text-center">
        <button class="btn btn-sm bg-primary" @click.prevent="$emit('editAction', item)">
            <i class="fa fa-edit" ></i>
        </button>&nbsp;
         <button class="btn btn-sm bg-danger" @click.prevent="$emit('confirmDeletion', item.menu_id)">
            <i class="fa fa-trash "></i>
        </button>

        </td>
    </tr>
</template>

<style scoped>
.custom-link {
  color: black; /* or any other color you prefer */
  text-decoration: none; /* Optional: Remove underline */
}
</style>
