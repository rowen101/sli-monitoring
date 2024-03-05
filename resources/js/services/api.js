import axios from "axios";
import { useToastr } from "../toastr.js";


const toastr = useToastr();
const baseURL = "api/";

const instance = axios.create({
    baseURL: baseURL,
    headers: {
        Accept: "application/json",
    },
});

instance.interceptors.request.use(
    async (config) => {
      try {
        const userToken = await localStorage.getItem("token");

        if (userToken) {
          config.headers["Authorization"] = `Bearer ${userToken}`;
        }

        return config;
      } catch (error) {
        return Promise.reject(error);
      }
    },
    (error) => Promise.reject(error)
  );

function httpMsg(obj, statuscode, msg) {
    if (statuscode === 401) {
        obj.$store.dispatch("logout").then(() => {
            obj.$router.push("/login");
        });
    } else if (statuscode === 200) {
        toastr.success(msg);
    } else if (statuscode === undefined) {
        toastr.error("Please contact administrator!!!");
    } else {
        toastr.error(msg);
    }
}

export default {
    instance,
    baseURL,
    httpMsg,
};
