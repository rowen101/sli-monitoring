<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";
import axios from "axios";
import { useAuthUserStore } from "../../stores/AuthUserStore";

const toastr = useToastr();
const authUserStore = useAuthUserStore();
const props = defineProps({
    item: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits([
    "userDeleted",
    "editUser",
    "confirmUserDeletion",
    "showItem",
]);

const toggleSelection = () => {
    emit("toggleSelection", props.item);
};
</script>
<template>
    <tr>
        <td>{{ item.recommnum }}</td>
        <td>{{ item.user }}</td>
        <td>{{ item.branch }}</td>
        <td>{{ item.department }}</td>
        <td>{{ item.created_user }}</td>
        <td style="text-align: center;">
            <router-link v-if="item.statusid ==2"
                :to="`/admin/tech-approved/${item.recommnum}/view`"
                target="_blank"
            >
                <span class="badge" :class="`badge-${item.status.color}`">
                    {{ item.status.name }}
                </span>
            </router-link>

            <span v-if="item.statusid !=2" class="badge" :class="`badge-${item.status.color}`">
                {{ item.status.name }}
            </span>

            <!-- &nbsp;<span v-if="item.status.name === 'APPROVED'" class="badge" :class="`badge-${item.status.color}`" v-html="getStatusHtml(item.status)"></span> -->
        </td>

        <td>{{ item.created_at }}</td>

        <td v-if="authUserStore.user.role === 'ADMIN'">
            <a href="#" @click.prevent="$emit('editUser', item)"
                ><i class="fa fa-edit"></i
            ></a>

            <a href="#" @click.prevent="$emit('confirmUserDeletion', item.id)"
                ><i class="fa fa-trash text-danger ml-2"></i
            ></a>
        </td>
        <td v-if="authUserStore.user.role === 'USER'" style="text-align: center;">
            <div v-if="item.statusid !=2">
                <a v-if="item.created_by == authUserStore.user.id" href="#" @click.prevent="$emit('editUser', item)"
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
