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
            return "Pending";
        case "A":
            return "Approved";
        case "C":
            return "Reject";
        default:
            return "badge-secondary";
    }
};
</script>
<template>
    <tr>
        <!-- <td>
            <input
                type="checkbox"
                :checked="selectAll"
                @change="toggleSelection"
            />
        </td> -->
        <td>{{ index + 1 }}</td>
        <td>{{ item.site_name }}</td>
        <td>{{ item.job_order_number }}</td>
        <td>{{ item.date_needed }}</td>
        <td>{{ item.commitment_date }}</td>
        <td style="text-align: center">
            <router-link

                :to="`/job-order-request-approved/${item.job_order_number}/view`"
                target="_blank"
            >
                <span class="badge" :class="`${statusClass(item.status)}`">
                    {{ statusClassdiscription(item.status) }}
                </span>
            </router-link>


        </td>
        <td>{{ item.created_user }}</td>
        <td>{{ item.created_at }}</td>

        <td v-if="authUserStore.user.role === 'ADMIN' || authUserStore.user.sitehead_user_id === authUserStore.user.id">


            <div @click.prevent="$emit('approvedata', item)"
                style="background-color:#17ad49; cursor: pointer; border:none;border-radius:4px;width:120px;height:32px;color:#ffffff;font-size:16px;font-style:normal;font-weight:500;font-family:&quot;Roboto&quot;,&quot;Arial&quot;;line-height:32px;display:inline-block;float:left;margin:3px;text-align:center">
                <span
                    style="color:#fff;text-decoration:none" target="_blank"
                    >Approved</span>
            </div>
            <div @click.prevent="$emit('confirmDataDeletion', item)"
                style="background-color:#da2f38; cursor: pointer; border:none;border-radius:4px;width:120px;height:32px;color:#ffffff;font-size:16px;font-style:normal;font-weight:500;font-family:&quot;Roboto&quot;,&quot;Arial&quot;;line-height:32px;display:inline-block;float:left;margin:3px;text-align:center">
                <span
                    style="color:#fff;text-decoration:none" target="_blank"
                    >Reject</span>
            </div>
        </td>
        <td
            v-if="authUserStore.user.role === 'USER'"
            style="text-align: center"
        >
            <div v-if="item.status != 'P'">
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
