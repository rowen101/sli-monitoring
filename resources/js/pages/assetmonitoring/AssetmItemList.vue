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
    <tr>
        <td>
            <input
                type="checkbox"
                :checked="selectAll"
                @change="toggleSelection"
            />
        </td>
        <td>{{ index + 1 }}</td>
        <td>{{ item.site_name }}</td>
        <td>{{ item.asset_name }}</td>
        <td>{{ item.asset_type }}</td>
        <td>{{ item.serial }}</td>
        <td>{{ item.date_acquired }}</td>
        <td>{{ item.man_supplier }}</td>
        <td>{{ item.unit }}</td>
        <td>{{ item.created_by }}</td>
        <td>{{ item.created_at }}</td>
        <td>{{ item.updated_by }}</td>

            <td class="text-center">
                <i class="fa fa-eye text-success" @click.prevent="$emit('viewData', item)"></i>
&nbsp;
            <i class="fa fa-edit text-primary" @click.prevent="$emit('editData', item)"></i>
       &nbsp;

            <i class="fa fa-trash text-danger" @click.prevent="$emit('confirmDeletion', item.id)"></i>



        </td>
    </tr>
</template>

<style scoped>
.custom-link {
    color: black; /* or any other color you prefer */
    text-decoration: none; /* Optional: Remove underline */
}
</style>
