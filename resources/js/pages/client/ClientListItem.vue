<script setup>
import { formatDate } from '../../helper.js';
import { ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';

const toastr = useToastr();

const props = defineProps({
    item: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.item);
}
</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index + 1 }}</td>
        <td>{{ item.first_name }} {{ item.last_name }} </td>
        <td>{{ item.email }}</td>
         <td>{{ item.created_at }}</td>

        <!-- <td>{{ item.formatted_created_at }}</td> -->

        <td>
            <a href="#" @click.prevent="$emit('editUser', item)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmUserDeletion', item.id)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
