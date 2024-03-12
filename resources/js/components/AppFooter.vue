<script setup>
import { useAuthUserStore } from '../stores/AuthUserStore';
import { ref } from 'vue';
const authUserStore = useAuthUserStore();
const year = ref(new Date().getFullYear());
defineProps({
    settings: Object,
});
</script>

<template>
    <footer class="main-footer">

        <div class="float-right d-none d-sm-block">
            {{ authUserStore.user.name }}
        </div>

        <strong>Copyright &copy; {{year}} <a href="#">{{ settings?.app_name }}</a>.</strong> All rights
        reserved.
    </footer>
</template>
