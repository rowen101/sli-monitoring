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

const emit = defineEmits(["userDeleted", "editMenu", "confirmDeletion"]);

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
        <td>{{ item.asset_name }}</td>
        <td>{{ item.asset_type }}</td>
        <td>{{ item.serial }}</td>
        <td>{{ item.date_acquired }}</td>
        <td>{{ item.man_supplier }}</td>
        <td>{{ item.unit }}</td>
        <td>{{ item.location }}</td>
        <td>
            {{ item.paccountable }}
        </td>
    </tr>
</template>

<style scoped>
.custom-link {
    color: black; /* or any other color you prefer */
    text-decoration: none; /* Optional: Remove underline */
}
</style>
