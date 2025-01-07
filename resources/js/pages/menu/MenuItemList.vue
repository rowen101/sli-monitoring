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

const emit = defineEmits(["userDeleted", "editMenu", "confirmDeletion"]);

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
        <td style="text-align: center;">
            <i :class="item.is_active === true ? 'fas fa-check' : 'far fa-circle'"></i>
        </td>




        <td>{{ item.created_at }}</td>
          <td class="text-center">
        <button class="btn btn-sm bg-primary" @click.prevent="$emit('editMenu', item)">
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
