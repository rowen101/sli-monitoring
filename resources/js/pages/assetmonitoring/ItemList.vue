<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";
import axios from "axios";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import DOMPurify from "dompurify";
const toastr = useToastr();
const authUserStore = useAuthUserStore();
const props = defineProps({
    item: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(["userDeleted", "editDa", "confirmDeletion"]);

const toggleSelection = () => {
    emit("toggleSelection", props.item);
};
</script>
<template>
    <tr >
        <td>
            <input
                type="checkbox"
                :checked="selectAll"
                @change="toggleSelection"
            />
        </td>
        <td>{{ index + 1 }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.itemcode }}</td>
        <td>{{ item.description }}</td>
        <td>{{ item.category }}</td>
        <td style="text-align: center;">
            <i :class="item.status === true ? 'fas fa-check' : 'far fa-circle'"></i>
        </td>
        <td>{{ item.created_by }}</td>
        <td>{{ item.created_at }}</td>
        <td>{{ item.updated_by }}</td>
        <td>{{ item.updated_at }}</td>


            <td class="text-center">
                <div>

            <i class="fa fa-edit text-primary" @click.prevent="$emit('editData', item)"></i>
       &nbsp;

            <i class="fa fa-trash text-danger" @click.prevent="$emit('confirmDeletion', item.id)"></i>


                </div>


        </td>
    </tr>
</template>

<style scoped>
.custom-link {
    color: black; /* or any other color you prefer */
    text-decoration: none; /* Optional: Remove underline */
}
</style>
