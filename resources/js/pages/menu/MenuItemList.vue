<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";
import axios from "axios";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import DOMPurify from 'dompurify';
const toastr = useToastr();
const authUserStore = useAuthUserStore();
const props = defineProps({
    item: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(["userDeleted", "editUser", "confirmUserDeletion","showItem"]);

const toggleSelection = () => {
    emit("toggleSelection", props.item);
};


</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index + 1 }}</td>
        <td>{{ item.menu_title }}</td>
        <td>{{ item.parent_title }}</td>
         <td>{{ item.menu_route }}</td>
       <td style="text-align: center;" v-html="`<i class='fa ${item.menu_icon}'></i>`"></td>
        <td>{{ item.sort_order }}</td>
        <td style="text-align: center;"><i :class="{'fas fa-check-circle': item.is_active === 1, 'far fa-circle': item.is_active !== 1}"></i></td>
        <td>{{ item.created_at }}</td>
          <td class="text-center">
        <button class="btn btn-sm bg-primary" @click.prevent="$emit('editUser', item)">
            <i class="fa fa-edit" ></i>
        </button>&nbsp;
         <button class="btn btn-sm bg-danger" @click.prevent="$emit('trash', item)">
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
