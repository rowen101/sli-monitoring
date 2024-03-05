<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use App\Models\User;
use App\Models\ListTask;
use App\Models\Appointment;
use App\Enums\AppointmentStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardStatController extends Controller
{
    public function appointments()
    {
        $totalAppointmentsCount = Appointment::query()
            ->when(request('status') === 'scheduled', function ($query) {
                $query->where('status', AppointmentStatus::SCHEDULED);
            })
            ->when(request('status') === 'confirmed', function ($query) {
                $query->where('status', AppointmentStatus::CONFIRMED);
            })
            ->when(request('status') === 'cancelled', function ($query) {
                $query->where('status', AppointmentStatus::CANCELLED);
            })
            ->count();

        return response()->json([
            'totalAppointmentsCount' => $totalAppointmentsCount,
        ]);
    }

    public function users()
    {
        $totalUsersCount = User::query()
            ->when(request('date_range') === 'today', function ($query) {
                $query->whereBetween('created_at', [now()->today(), now()]);
            })
            ->when(request('date_range') === '30_days', function ($query) {
                $query->whereBetween('created_at', [now()->subDays(30), now()]);
            })
            ->when(request('date_range') === '60_days', function ($query) {
                $query->whereBetween('created_at', [now()->subDays(60), now()]);
            })
            ->when(request('date_range') === '360_days', function ($query) {
                $query->whereBetween('created_at', [now()->subDays(360), now()]);
            })
            ->when(request('date_range') === 'month_to_date', function ($query) {
                $query->whereBetween('created_at', [now()->firstOfMonth(), now()]);
            })
            ->when(request('date_range') === 'year_to_date', function ($query) {
                $query->whereBetween('created_at', [now()->firstOfYear(), now()]);
            })
            ->count();

        return response()->json([
            'totalUsersCount' => $totalUsersCount,
        ]);
    }



    public function getChartData()
    {
        // Get the authenticated user's ID
        $userId = auth()->user()->id;
        $currentYear = date('Y');
        // Fetch data from tbl_dailytask and join with tbl_tasklist
        $tasks = DB::table('tbl_dailytask')
            ->selectRaw('DATE_FORMAT(taskdate, "%Y-%m") as month, COUNT(*) as total_tasks')
            ->where('user_id', $userId)
            ->where('status_task','=', 1)
            ->whereYear('taskdate', '=', $currentYear) // Filter for the current year
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fetch data from tbl_tasklist where iscompleted = 1
        $completedTasks = DB::table('tbl_tasklist')
            ->selectRaw('DATE_FORMAT(tbl_tasklist.created_at, "%Y-%m") as month, COUNT(*) as completed_todos')
            ->join('tbl_dailytask', 'tbl_dailytask.dailytask_id', '=', 'tbl_tasklist.dailytask_id')
            ->where('tbl_tasklist.iscompleted', 1)
            ->where('tbl_dailytask.user_id', $userId)
            ->whereYear('tbl_dailytask.taskdate', '=', $currentYear) // Filter for the current year
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fetch data from tbl_tasklist where iscompleted = 0
        $incompleteTasks = DB::table('tbl_tasklist')
            ->selectRaw('DATE_FORMAT(tbl_tasklist.created_at, "%Y-%m") as month, COUNT(*) as incomplete_todos')
            ->join('tbl_dailytask', 'tbl_dailytask.dailytask_id', '=', 'tbl_tasklist.dailytask_id')
            ->where('tbl_tasklist.iscompleted', 0)
            ->where('tbl_dailytask.user_id', $userId)
            ->whereYear('tbl_dailytask.taskdate', '=', $currentYear) // Filter for the current year
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

        $chartData = [
            'labels' => $monthNames,
            'datasets' => [
                [
                    'label' => 'Total Close Tasks',
                    'data' => $tasks->pluck('total_tasks'),
                    'backgroundColor' => '#1976D2',
                ],
                [
                    'label' => 'Completed Todos',
                    'data' => $completedTasks->pluck('completed_todos'),
                    'backgroundColor' => '#00B489',
                ],
                [
                    'label' => 'Incomplete Todos',
                    'data' => $incompleteTasks->pluck('incomplete_todos'),
                    'backgroundColor' => '#CD201F',
                ],
            ],
        ];

        return response()->json($chartData);
    }

}
