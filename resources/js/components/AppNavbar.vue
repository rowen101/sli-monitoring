<script setup>
import { useSettingStore } from '../stores/SettingStore';
import { ref, onMounted, reactive, watch } from "vue";
import axios from "axios";
const settingStore = useSettingStore();
import moment from 'moment';
const notifications = ref([]);

const fetchNotifications = () =>{
     axios.get('/api/notifications')
        .then(response => {
          notifications.value = response.data.notifications;
        })
        .catch(error => {
          console.error('Error fetching notifications', error);
        });
}

const formatDate =(date) =>{
    return moment(date).format('YYYY-MM-DD HH:mm:ss');
}

onMounted(() => {
    fetchNotifications();
});
</script>

<template>
    <nav class="main-header navbar navbar-expand" :class="settingStore.theme === 'dark' ? 'navbar-dark': 'navbar-light'">

        <ul class="navbar-nav">
            <li class="nav-item" id="toggleMenuIcon">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <!-- <li class="nav-item d-none d-sm-inline-block">
                <a href="index3.html" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Contact</a>
            </li> -->

            <li class="nav-item d-none d-sm-inline-block">
                <a @click.prevent="settingStore.changeTheme" href="#" class="nav-link">
                    <i class="far" :class="settingStore.theme === 'dark' ? 'fa-moon' : 'fa-sun'"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="fas fa-search"></i>
                </a>
                <div class="navbar-search-block">
                    <form class="form-inline">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>

            <li class="nav-item dropdown">
                <!-- <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-comments"></i>
                    <span class="badge badge-danger navbar-badge">3</span>
                </a> -->
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="#" class="dropdown-item">

                        <div class="media">
                            <img src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg" alt="User Avatar"
                                class="img-size-50 mr-3 img-circle">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Brad Diesel
                                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm">Call me whenever you can...</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>

                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">

                        <div class="media">
                            <img src="https://adminlte.io/themes/v3/dist/img/user8-128x128.jpg" alt="User Avatar"
                                class="img-size-50 img-circle mr-3">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    John Pierce
                                    <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm">I got your message bro</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>

                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">

                        <div class="media">
                            <img src="https://adminlte.io/themes/v3/dist/img/user3-128x128.jpg" alt="User Avatar"
                                class="img-size-50 img-circle mr-3">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Nora Silvester
                                    <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm">The subject goes here</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>

                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                </div>
            </li>

           <li class="nav-item dropdown" @click="fetchNotifications">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge">{{ notifications.length }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header">{{ notifications.length }} Notifications</span>
        <div class="dropdown-divider"></div>
        <template v-if="notifications.length > 0">
            <a v-for="notification in notifications" :key="notification.id" href="#" class="dropdown-item">
                <i class="fas fa-envelope mr-2"></i> {{ notification.data ? notification.data : 'No message available' }}
                <span class="float-right text-muted text-sm">{{moment(notification.created_at)}}</span>
            </a>
        </template>
        <template v-else>
            <p class="text-center text-muted py-3">No new notifications</p>
        </template>
        <div class="dropdown-divider"></div>
        <router-link to="notifications" class="dropdown-item dropdown-footer">See All Notifications</router-link>
    </div>
</li>

            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
        </ul>
    </nav>
</template>
