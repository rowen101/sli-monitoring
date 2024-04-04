<script setup>
import axios from "axios";
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthUserStore } from "../../stores/AuthUserStore";
import { useToastr } from "../../toastr.js";

const showPassword = ref(false);

const toastr = useToastr();
const authUserStore = useAuthUserStore();
const router = useRouter();
const form = reactive({
    email: "",
    password: "",
});

const loading = ref(false);
const pageTitle = `Login`;
const errorMessage = ref("");
const handleSubmit = () => {
    loading.value = true;
    errorMessage.value = "";
    axios
        .post("/login", form)
        .then(() => {
            router.push("/dashboard");
            toastr.success("Login Success");
        })
        .catch((error) => {
            //errorMessage.value = error.response.data.message;
            toastr.error(error.response.data.message);
        })
        .finally(() => {
            loading.value = false;
        });
};

// const handleKeyClick =() =>{
//     alert(form.email);
// }
onMounted(() => {
    document.title = pageTitle;
});
</script>

<template>
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h5">Sign in</a>
            </div>
            <div class="card-body">
                <div
                    v-if="errorMessage"
                    class="alert alert-danger"
                    role="alert"
                >
                    {{ errorMessage }}
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <input
                            v-model="form.email"
                            type="email"
                            class="form-control"
                            placeholder="Email"

                        />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <input
                            v-model="form.password"
                            class="form-control"
                            placeholder="Password"
                            :type="showPassword ? 'text' : 'password'"
                            @click="handleKeyClick"
                        />
                    </div>
                    <label style="font-family: Tahoma, Geneva, sans-serif;font-size: 12px;">
                        <input type="checkbox" v-model="showPassword" />
                        Show Password
                    </label>
                    <div class="input-group mb-3">

                      <!-- <select  class="form-control" v-model="form.site">
                        <option v-for="item in listuser" :key="item.id" :value="item.id"  :selected="(user.sitehead_user_id === item.id)">{{ item.first_name +' '+ item.last_name }}</option>
                    </select> -->

                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <button
                                type="submit"
                                class="btn btn-primary btn-block"
                                :disabled="loading"
                            >
                                <div
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm"
                                    role="status"
                                >
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span v-else>Sign In</span>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-12">
                        <a
                            class="btn btn-success btn-block"
                            href="https://ticket.appsafexpress.com/open.php"
                            target="_blank"
                        >
                            <span><b>Request Account</b></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>

.bg-login {
    /* Default background image for larger screens */
    background-image: url("/img/bg.jpg");
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
    margin: 0; /* Remove default body margin */
    height: 100vh; /* Set body height to 100% of viewport height */
}

input::-ms-reveal,
input::-ms-clear {
    display: none;
}
</style>

