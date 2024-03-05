<script setup>
import { formatDate } from '../../helper.js';
import { ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';

const toastr = useToastr();

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion']);



const toggleSelection = () => {
    emit('toggleSelection', props.user);
}
</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index + 1 }}</td>
        <td>{{ user.first_name + ' '+ user.last_name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.formatted_created_at }}</td>

        <!-- <td>
            <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="(user.role === role.name)">{{ role.name }}</option>
            </select>
        </td> -->
        <td class="text-center">
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>

        </td>
    </tr>
</template>
