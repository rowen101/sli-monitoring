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
        <td @click.prevent="$emit('viewData', item)">{{ item.site_name }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.asset_name }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.asset_type }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.serial }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.date_acquired }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.man_supplier }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.unit }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ '₱' + item.purchasecost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ '₱' + item.depreciationcostbyyear.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.created_by }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.created_at }}</td>
        <td @click.prevent="$emit('viewData', item)">{{ item.updated_by }}</td>

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
