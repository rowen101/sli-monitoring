<script setup>
import { formatDate } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";
import axios from "axios";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import DOMPurify from "dompurify";
import moment from "moment";


const viewcost = ref(true);
const toastr = useToastr();
const authUserStore = useAuthUserStore();
const props = defineProps({
    item: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(["dataDeleted", "editData", "confirmDeletion","viewcost"]);

const toggleSelection = () => {
    emit("toggleSelection", props.item);
};
</script>
<template>
    <tr style="text-align:center;">
        <td>
            <input
                type="checkbox"
                :checked="selectAll"
                @change="toggleSelection"
            />
        </td>

        <td>{{ moment(item.date).format("MMMM DD, YY")}}</td>

        <td >{{ item.allocatedpalletspace }}</td>
        <td>{{ item.spaceuteltotal }}</td>
        <td>{{ item.spacetotalutelpercent }}</td>
        <td>{{ item.excess }}</td>
        <td v-if="$emit('viewcost')">{{ item.caseperpallet }}</td>
        <td v-if="$emit('viewcost')">{{item.cost}}</td>
        <td>EXCESS {{ item.excess }} PALLETS</td>

        <td>
            <a href="#" @click.prevent="$emit('editData', item)"
                ><i class="fa fa-edit"></i
            ></a>

            <!-- <a href="#" @click.prevent="$emit('confirmUserDeletion', item.id)"
                ><i class="fa fa-trash text-danger ml-2"></i
            ></a> -->
        </td>
    </tr>

</template>

<style scoped>
.custom-link {
    color: black; /* or any other color you prefer */
    text-decoration: none; /* Optional: Remove underline */
}
</style>
