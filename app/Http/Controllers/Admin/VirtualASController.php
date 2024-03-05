<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use App\Models\ThemeVsc;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class VirtualASController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = auth()->user()->id;

        // Get the start and end of the current week
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Get the start and end of the next week
        $startOfNextWeek = Carbon::now()->startOfWeek()->addWeek();
        $endOfNextWeek = Carbon::now()->endOfWeek()->addWeek();

        // Get tasks for the current week
        $dailyTasks = Task::orderBy('tbl_dailytask.taskdate', 'asc')
            ->join('tbl_sites', 'tbl_sites.id', '=', 'tbl_dailytask.site')
            ->join('theme_vscs', 'theme_vscs.userid', '=', 'tbl_dailytask.user_id')
            ->with('taskLists')
            ->where('user_id', $userId)
            ->whereBetween('tbl_dailytask.taskdate', [$startOfWeek, $endOfWeek])
            ->select('tbl_dailytask.*', 'tbl_sites.site_name','theme_vscs.id as theme_id','theme_vscs.background',
            'theme_vscs.active_background','theme_vscs.font_background')
            ->orderBy('taskdate', 'asc')
            ->get();

        // Get tasks for the next week
        $nextWeekTasks = Task::orderBy('tbl_dailytask.taskdate', 'asc')
            ->join('tbl_sites', 'tbl_sites.id', '=', 'tbl_dailytask.site')
            ->join('theme_vscs', 'theme_vscs.userid', '=', 'tbl_dailytask.user_id')
            ->with('taskLists')
            ->where('user_id', $userId)
            ->whereBetween('tbl_dailytask.taskdate', [$startOfNextWeek, $endOfNextWeek])
            ->select('tbl_dailytask.*', 'tbl_sites.site_name','theme_vscs.id as theme_id','theme_vscs.background',
            'theme_vscs.active_background','theme_vscs.font_background')
            ->orderBy('taskdate', 'asc')
            ->get();

        // Determine if there are tasks for the next week
        $hasNextWeekTasks = !$nextWeekTasks->isEmpty();

        // Use the appropriate set of tasks based on the presence of next week's tasks
        $allTasks = $hasNextWeekTasks ? $dailyTasks->merge($nextWeekTasks) : $dailyTasks;

        $tasksList = Task::withCount([
            'taskLists',
            'taskLists as completed_task_count' => function ($query) {
                $query->where('iscompleted', 1);
            }
        ])
            ->where('user_id', $userId)
            ->whereIn('taskdate', $allTasks->pluck('taskdate')->unique())
            ->orderBy('taskdate', 'asc')
            ->get();

        // Calculate the percentage of completed tasks
        $tasksList->transform(function ($task) {
            $task->percentage_completed = round(
                ($task->task_lists_count > 0)
                    ? ($task->completed_task_count / $task->task_lists_count) * 100
                    : 0,
                2
            );

            return $task;
        });

        return response()->json(['dailyTasks' => $allTasks, 'TaskList' => $tasksList, 'hasNextWeekTasks' => $hasNextWeekTasks]);
    }


    public function vscfilter(Request $request)
    {
        $userId = auth()->user()->id;

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $dailyTasks = Task::orderBy('tbl_dailytask.taskdate', 'asc')
            ->join('tbl_sites', 'tbl_sites.id', '=', 'tbl_dailytask.site')
            ->join('theme_vscs', 'theme_vscs.userid', '=', 'tbl_dailytask.user_id')
            ->with('taskLists')
            ->where('user_id', $userId)
            ->whereBetween('tbl_dailytask.taskdate', [$startDate, $endDate])
            ->select('tbl_dailytask.*', 'tbl_sites.site_name','theme_vscs.id as theme_id','theme_vscs.background',
            'theme_vscs.active_background','theme_vscs.font_background')
            ->get();


        $tasksList = Task::withCount([
            'taskLists',
            'taskLists as completed_task_count' => function ($query) {
                $query->where('iscompleted', 1);
            }
        ])
            ->where('user_id', $userId)
            ->whereBetween('tbl_dailytask.taskdate', [$startDate, $endDate])
            ->orderBy('taskdate', 'asc')
            ->get();

        // Calculate the percentage of completed tasks
        $tasksList->transform(function ($task) {
            $task->percentage_completed = ($task->task_lists_count > 0)
                ? ($task->completed_task_count / $task->task_lists_count) * 100
                : 0;
            return $task;
        });



        return response()->json(['dailyTasks' => $dailyTasks, 'TaskList' => $tasksList]);
    }

    public function changethemes(Request $request){

        ThemeVsc::updateOrCreate(
            [
                'userid' => $request->userid,
            ],
            [

                'background' => $request->background,
                'active_background' => $request->active_background,
                'font_background' => $request->font_background
            ]
        );

        return response()->json(['message' => 'success']);
    }
}
