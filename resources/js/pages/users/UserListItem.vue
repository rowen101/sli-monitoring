<script setup>
import { formatDate } from '../../helper.js';
import { ref,onMounted } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';

const toastr = useToastr();

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion']);

const roles = ref([
    {
        name: 'ADMIN',
        value: 1
    },
    {
        name: 'USER',
        value: 2,
    }
]);

const listuser = ref([]);

const getOptionUsers = () => {
    axios.get(`/api/users/userlist` )
    .then((response) => {
        listuser.value = response.data;

    })
    .catch((error) => {
        console.log(error);
    });
}

const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
    .then(() => {
        toastr.success('Role changed successfully!');
    })
};
const changeSitehead = (user, item) => {
    axios.patch(`/api/users/${user.id}/change-sitehead`, {
        sitehead_user_id: item,
    })
    .then(() => {
        toastr.success('Sitehead changed successfully!');
    })
};
const toggleSelection = () => {
    emit('toggleSelection', props.user);
}

onMounted(() => {
    getOptionUsers();
});
</script>
<template>
    <tr>
        <td ><input v-if="user.name != 'admin'" type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index + 1 }}</td>
        <td>{{ user.first_name +' '+ user.last_name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.formatted_created_at }}</td>
        <td>
            <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :key="role.id"  :value="role.value"  :selected="(user.role === role.name)">{{ role.name }}</option>
            </select>
        </td>
        <td >
            <select v-if="user.name != 'admin'" class="form-control" @change="changeSitehead(user, $event.target.value)">
                <option v-for="item in listuser" :key="item.id" :value="item.id"  :selected="(user.sitehead_user_id === item.id)">{{ item.first_name +' '+ item.last_name }}</option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
            <a v-if="user.id != 1" href="#" @click.prevent="$emit('confirmUserDeletion', user.id)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
