<script setup>
import { ref, inject } from "vue";
import { useToastr } from "@/toastr.js";
import axios from "axios";
import { useAuthUserStore } from "@/stores/AuthUserStore.js";
import moment from "moment";

const toastr = useToastr();
const authUserStore = useAuthUserStore();
const swal = inject("$swal");

const props = defineProps({
  item: Object,
  index: Number,
  selectAll: Boolean,
});

const emit = defineEmits(["userDeleted", "editAction", "confirmDeletion", "toggleSelection"]);

const isLoading = ref(false);

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

const statusDescription = (status) => {
  switch (status) {
    case "P":
      return "Pending";
    case "A":
      return "Approved";
    case "C":
      return "Rejected";
    default:
      return "Unknown";
  }
};
;
</script>

<template>
  <div v-if="isLoading" class="spinner">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden"></span>
    </div>
  </div>
  <tr>
    <td>{{ index + 1 }}</td>
    <td>{{ item.site_name }}</td>
    <td>{{ item.mrf_order_number }}</td>
    <td>{{ moment(item.date_requested).format("MMMM D, YYYY") }}</td>
    <td>{{ moment(item.date_needed).format("MMMM D, YYYY") }}</td>
    <td style="text-align: center">
      <router-link
        :to="`/job-order-request-approved/${item.job_order_number}/view`"
        target="_blank"
      >
        <span class="badge" :class="`${statusClass(item.status)}`">
          {{ statusDescription(item.status) }}
        </span>
      </router-link>
    </td>
    <td>{{ item.created_user }}</td>
    <td>{{ moment(item.created_at).format("MMMM D, YYYY, h:mm A") }}</td>

    <td
      v-if="
        authUserStore.user.role === 'ADMIN' ||
        authUserStore.user.sitehead_user_id === authUserStore.user.id
      "
    >
      <select 
        class="form-control"
        @change="(event) => $emit('handleSelectChange', event, item)"
      >
        <option value="" disabled selected>Action</option>
        <option value="A">Approved</option>
        <option value="C">Reject</option>
      </select>
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
        >
          <i class="fa fa-edit"></i>
        </a>
      </div>
    </td>
  </tr>
</template>

<style scoped>
.custom-link {
  color: black;
  text-decoration: none;
}
.spinner {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
}
.spinner-border {
  width: 3rem;
  height: 3rem;
}
</style>
