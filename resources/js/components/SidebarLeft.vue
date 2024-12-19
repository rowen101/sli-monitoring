<script setup>
import { useAuthUserStore } from "../stores/AuthUserStore";
import { useRouter } from "vue-router";
import { useSettingStore } from "../stores/SettingStore";
import { onMounted, ref } from "vue";
const router = useRouter();
const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();

const menulist = ref({ data: [] });

const getmenu = () => {
    axios
        .get("/web/menu")
        .then((response) => {
            menulist.value = response.data;

        })
        .catch((error) => {
            console.log(error);
        });
};

const isCurrentRoute = (route) => {
    return route === router.currentRoute.value.path;
};

const logout = () => {
    axios.post("/logout").then((response) => {
        authUserStore.user.name = "";
        router.push("/login");
    });
};

onMounted(() => {
    $('[data-widget="treeview"]').Treeview("init");
    getmenu();
});
</script>

<template>
     <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <img
                :src="'/img/safe1.png'"
                alt=" Logo"
                class="brand-image"
                style="opacity: 0.8"
                 draggable="false"
            />
            <span class="brand-text font-weight-light">{{
                settingStore.setting.app_name
            }}</span>
        </a>

        <div class="sidebar" >

    <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul
                    class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview"
                    role="menu"
                    data-accordion="false"
                >
                    <li
                        v-for="item in menulist"
                        :key="item.id"
                        :class="{
                            'nav-item': true,
                            'menu-is-opening':
                                item.submenus && item.submenus.length > 0,
                        }"
                    >
                        <router-link
                            :class="{
                                'nav-link': true,
                                'active': isCurrentRoute(item.menu_route),
                            }"
                            :to="`${item.menu_route}`"
                        >
                            <i :class="'nav-icon ' + item.menu_icon"></i>
                            <p>{{ item.menu_title }}</p>
                            <i
                                v-if="item.submenus && item.submenus.length > 0"
                                class="right fas fa-angle-left"
                            ></i>
                        </router-link>



                        <ul
                            v-if="item.submenus && item.submenus.length > 0"
                            class="nav nav-treeview"
                        >
                            <li
                                    v-for="submenu in item.submenus"
                                    :key="submenu.id"
                                class="nav-item"
                            >
                                <router-link
                                    :class="{
                                        'nav-link menu-open': true,
                                        'active': isCurrentRoute(
                                            submenu.menu_route
                                        ),
                                    }"
                                    :to="submenu.menu_route"
                                >
                                    <i
                                        :class="'nav-icon ' + submenu.menu_icon"
                                    ></i>
                                    <p>{{ submenu.menu_title }}</p>
                                </router-link>

                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <form class="nav-link">
                            <a href="#" @click.prevent="logout">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>
