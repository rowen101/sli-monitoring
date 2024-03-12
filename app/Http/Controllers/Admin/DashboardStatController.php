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
            ->where('status_task', '=', 1)
            ->whereYear('taskdate', '=', $currentYear) // Filter for the current year
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fetch data from tbl_tasklist
        $allTodos = DB::table('tbl_tasklist')
            ->selectRaw('DATE_FORMAT(tbl_tasklist.created_at, "%Y-%m") as month, COUNT(*) as total_todos')
            ->join('tbl_dailytask', 'tbl_dailytask.dailytask_id', '=', 'tbl_tasklist.dailytask_id')
            ->where('tbl_dailytask.user_id', $userId)
            ->whereYear('tbl_dailytask.taskdate', '=', $currentYear) // Filter for the current year
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

        // Prepare month names
        $monthNames = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        // Initialize chart data array
        $chartData = [
            'labels' => array_values($monthNames), // Use month names as labels
            'datasets' => [
                [
                    'label' => 'Total Todos',
                    'data' => array_fill(0, 12, 0), // Initialize data array with zeros for each month
                    'backgroundColor' => '#1976D2',
                ],
                [
                    'label' => 'Completed Todos',
                    'data' => array_fill(0, 12, 0), // Initialize data array with zeros for each month
                    'backgroundColor' => '#00B489',
                ],
                [
                    'label' => 'Incomplete Todos',
                    'data' => array_fill(0, 12, 0), // Initialize data array with zeros for each month
                    'backgroundColor' => '#CD201F',
                ],
            ],
        ];

        // Populate chart data arrays with actual data
        foreach ($tasks as $task) {
            $monthIndex = intval(substr($task->month, 5)) - 1; // Get month index (0-based)
            $chartData['datasets'][0]['data'][$monthIndex] = $task->total_tasks;
        }

        foreach ($completedTasks as $completedTask) {
            $monthIndex = intval(substr($completedTask->month, 5)) - 1; // Get month index (0-based)
            $chartData['datasets'][1]['data'][$monthIndex] = $completedTask->completed_todos;
        }

        foreach ($incompleteTasks as $incompleteTask) {
            $monthIndex = intval(substr($incompleteTask->month, 5)) - 1; // Get month index (0-based)
            $chartData['datasets'][2]['data'][$monthIndex] = $incompleteTask->incomplete_todos;
        }

        // Populate total todos data
        foreach ($allTodos as $todo) {
            $monthIndex = intval(substr($todo->month, 5)) - 1; // Get month index (0-based)
            $chartData['datasets'][0]['data'][$monthIndex] = $todo->total_todos;
        }

        return response()->json($chartData);
    }


}
