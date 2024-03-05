<script setup>
import { ref, onMounted, computed } from "vue";
import AppNavbar from "./components/AppNavbar.vue";
import SidebarLeft from "./components/SidebarLeft.vue";
import SidebarRight from "./components/SidebarRight.vue";
import AppFooter from "./components/AppFooter.vue";
import { useAuthUserStore } from "./stores/AuthUserStore";
import { useSettingStore } from "./stores/SettingStore";

const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();

const currentThemeMode = computed(() => {
    return settingStore.theme === "dark" ? "dark-mode" : "";
});
</script>
<template>
    <div
        v-if="authUserStore.user.name !== ''"
        class="wrapper"
        :class="currentThemeMode"
    >
        <AppNavbar />
        <SidebarLeft />
        <SidebarRight />
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">{{ $route.name }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    {{ $route.name }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <router-view></router-view>
        </div>

        <AppFooter />
    </div>
    <div v-else class="login-page slibg" :class="currentThemeMode">
        <router-view></router-view>
    </div>
</template>
<style scoped>
.slibg {
    /* Default background image for larger screens */
    background-image: url("/img/bg.jpg");
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
    margin: 0; /* Remove default body margin */
    height: 100vh; /* Set body height to 100% of viewport height */
}
</style>
