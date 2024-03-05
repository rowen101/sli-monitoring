<script setup>
import { onMounted, ref } from 'vue';
import { useAuthUserStore } from "../stores/AuthUserStore";
import { Bar } from "vue-chartjs"
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
);


const authUserStore = useAuthUserStore();
const selectedAppointmentStatus = ref('all');
const totalAppointmentsCount = ref(0);


const chartData = ref({
      labels: [],
      datasets: [
        {
          data: [],
        },
        {
          data: [],
        },
        {
          data: [],
        },
      ],
    });

const getChart = () => {
      axios.get('/api/chart')
        .then((response) => {
        const data = response.data;
        chartData.value = response.data;

        })
        .catch((error) => {
          console.error('Error fetching chart data:', error);
        });
    };




const selectedDateRange = ref('today');
const totalUsersCount = ref(0);

const getUsersCount = () => {
    axios.get('/api/stats/users', {
        params: {
            date_range: selectedDateRange.value,
        }
    })
    .then((response) => {
        totalUsersCount.value = response.data.totalUsersCount;
    });
};

onMounted(() => {
    getChart();
    getUsersCount();
});
</script>
<template>


    <div class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    Yearly Progress
                </div>
                <div class="card-body">
            <Bar :data="chartData" />
                </div>

                 </div>
            <div class="row">
               
            </div>

        </div>
    </div>
</template>
