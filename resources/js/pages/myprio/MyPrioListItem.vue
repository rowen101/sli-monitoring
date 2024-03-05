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
 
});

const emit = defineEmits(["userDeleted", "editUser", "confirmUserDeletion","showItem"]);

const toggleSelection = () => {
    emit("toggleSelection", props.item);
};

const getStatusHtml = (status) => {
    if (status.name === 'APPROVED') {
        // Sanitize and return the HTML
        return DOMPurify.sanitize('<a   href="" target="_blank"><i class="fas fa-download" ></i></a>');
      } else {
        // Otherwise, return the status name
        return status.name;
      }
}
</script>
<template>
    <tr>
        <!-- <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td> -->
        <td>{{ index + 1 }}</td>
        <td>{{ item.recommnum }}</td>
        <td>{{ item.user }}</td>
        <td>{{ item.branch }}</td>
        <td>{{ item.department }}</td>
        <td>{{ item.created_by }}</td>
        <td>
            <span class="badge" :class="`badge-${item.status.color}`" >{{
                item.status.name
            }} </span>

            &nbsp;<span v-if="item.status.name === 'APPROVED'" class="badge" :class="`badge-${item.status.color}`" v-html="getStatusHtml(item.status)"></span>

        </td>

        <td>{{ item.created_at }}</td>


        <td v-if="authUserStore.user.role === 'ADMIN'">

            <a href="#" @click.prevent="$emit('editUser', item)"
                ><i class="fa fa-edit"></i
            ></a>
            <a href="#" @click.prevent="$emit('showItem', item)"
                ><i class="fa fa-eye"></i
            ></a>
            <a href="#" @click.prevent="$emit('confirmUserDeletion', item.id)"
                ><i class="fa fa-trash text-danger ml-2"></i
            ></a>
        </td>
        <td v-if="authUserStore.user.role === 'USER'">


            <a href="#" @click.prevent="$emit('editUser', item)"
                ><i class="fa fa-eye"></i
            ></a>

        </td>
    </tr>
</template>

<style scoped>
.custom-link {
  color: black; /* or any other color you prefer */
  text-decoration: none; /* Optional: Remove underline */
}
</style>
