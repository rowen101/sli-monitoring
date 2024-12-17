<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "@/toastr.js";
import axios from "axios";
import { useAuthUserStore } from "@/stores/AuthUserStore.js";
import DOMPurify from "dompurify";
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

const statusClass = (status) => {
    switch (status) {
        case "P":
            return "badge-warning";
        case "A":
            return "badge-success";
        case "C":
            return "badge-danger";
        default:
            return "badge-secondary";
    }
};
const statusClassdiscription = (status) => {
    switch (status) {
        case "P":
            return "Pinding";
        case "A":
            return "Approved";
        case "C":
            return "Closed";
        default:
            return "badge-secondary";
    }
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
        <td>{{ item.job_order_number }}</td>
        <td>{{ item.end_user }}</td>
        <td>{{ item.date_needed }}</td>
        <td>{{ item.commitment_date }}</td>
        <td>{{ item.commitment_date }}</td>
        <td style="text-align: center">
            <router-link
                v-if="item.status != 'C'"
                :to="`/job-order-request-approved/${item.job_order_number}/view`"
                target="_blank"
            >
                <span class="badge" :class="`${statusClass(item.status)}`">
                    {{ statusClassdiscription(item.status) }}
                </span>
            </router-link>

            <span v-else class="badge" :class="`${statusClass(item.status)}`">
                {{ statusClassdiscription(item.status) }}
            </span>
        </td>
        <td>{{ item.created_user }}</td>
        <td>{{ item.created_at }}</td>

        <td v-if="authUserStore.user.role === 'ADMIN'">
            <a href="#" @click.prevent="$emit('editRecord', item)"
                ><i class="fa fa-edit"></i
            ></a>

            <a href="#" @click.prevent="$emit('confirmUserDeletion', item)"
                ><i class="fa fa-trash text-danger ml-2"></i
            ></a>
        </td>
        <td
            v-if="authUserStore.user.role === 'USER'"
            style="text-align: center"
        >
            <div v-if="item.status != 'A'">
                <a
                    v-if="item.created_by == authUserStore.user.id"
                    href="#"
                    @click.prevent="$emit('editRecord', item)"
                    ><i class="fa fa-edit"></i
                ></a>
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
