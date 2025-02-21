<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Backend\TodoRequest;
use App\Http\Resources\Web\Backend\TodoResource;
use App\Jobs\SendReminderEmailJob;
use App\Models\Todo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class TodoController extends Controller {
    /**
     * Display a listing of todos.
     *
     * @param Request $request
     * @return View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request): View | JsonResponse {
        try {
            if ($request->ajax()) {
                $data = Todo::latest()->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('due_date', function ($todo) {
                        return $todo->due_date->format('Y-m-d H:i');
                    })
                    ->addColumn('reminder_email_sent', function ($todo) {
                        return $todo->reminder_email_sent
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-danger">No</span>';
                    })
                    ->addColumn('status', function ($todo) {
                        $status = '<div class="form-check form-switch" style="margin-left: 40px; width: 50px; height: 24px;">';
                        $status .= '<input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck' . $todo->id . '" ' . ($todo->status == 'active' ? 'checked' : '') . ' onclick="showStatusChangeAlert(' . $todo->id . ')">';
                        $status .= '</div>';
                        return $status;
                    })
                    ->addColumn('action', function ($todo) {
                        return '
                        <div class="hstack gap-3 fs-base">
                            <a href="' . route('todos.edit', $todo->id) . '" class="link-primary text-decoration-none" title="Edit">
                                <i class="ri-pencil-line" style="font-size: 24px;"></i>
                            </a>

                            <a href="javascript:void(0);" onclick="showTodoDetails(' . $todo->id . ')" class="link-primary text-decoration-none" title="View" data-bs-toggle="modal" data-bs-target="#viewTodoModal">
                                <i class="ri-eye-line" style="font-size: 24px;"></i>
                            </a>

                            <a href="javascript:void(0);" onclick="showDeleteConfirm(' . $todo->id . ')" class="link-danger text-decoration-none" title="Delete">
                                <i class="ri-delete-bin-5-line" style="font-size: 24px;"></i>
                            </a>
                        </div>
                    ';
                    })
                    ->rawColumns(['due_date', 'reminder_email_sent', 'status', 'action'])
                    ->make();
            }
            return view('backend.layouts.todos.index');
        } catch (Exception $e) {
            Log::error('Error fetching todos: ' . $e->getMessage());
            return Helper::jsonResponse(false, 'Failed to load todos.', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified todo.
     *
     * @param  Todo  $todo
     * @return JsonResponse
     */
    public function show(Todo $todo): JsonResponse {
        try {
            return Helper::jsonResponse(true, "Todo details retrieved successfully.", 200, new TodoResource($todo));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, "Failed to retrieve Todo details.", 500, [$e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new todo.
     *
     * @return View|RedirectResponse
     */
    public function create(): View | RedirectResponse {
        try {
            return view('backend.layouts.todos.create');
        } catch (Exception $e) {
            Log::error('Error loading create todo page: ' . $e->getMessage());
            return redirect()->route('todos.index')->with('t-error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Store a newly created todo.
     *
     * @param TodoRequest $request
     * @return RedirectResponse
     */
    public function store(TodoRequest $request): RedirectResponse {
        try {
            $data = $request->validated();

            $data              = new Todo();
            $data->title       = $request->title;
            $data->email       = $request->email;
            $data->due_date    = Carbon::parse($request->due_date);
            $data->description = $request->description;
            $data->save();

            Log::info('New Todo created', [
                'todo_id'  => $data->id,
                'email'    => $data->email,
                'due_date' => $data->due_date->format('Y-m-d H:i:s'),
            ]);

            $reminder = $data->due_date->copy()->subMinutes(10);
            $delay    = now()->diffInSeconds($reminder, false);
            $delay    = $delay > 0 ? $delay : 0;

            Log::info('Reminder calculation details', [
                'reminder_time' => $reminder->format('Y-m-d H:i:s'),
                'delay_seconds' => $delay,
            ]);

            dispatch(new SendReminderEmailJob($data))->delay($delay);

            Log::info('Dispatching SendReminderEmailJob with delay', ['delay' => $delay]);

            return redirect()->route('todos.index')->with('t-success', 'Todo created successfully');
        } catch (Exception $e) {
            Log::error('Error creating todo: ' . $e->getMessage());

            return redirect()->back()->with('t-error', 'Something went wrong. Please try again.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified todo.
     *
     * @param Todo $todo
     * @return View|RedirectResponse
     */
    public function edit(Todo $todo): View | RedirectResponse {
        try {
            return view('backend.layouts.todos.edit', compact('todo'));
        } catch (Exception $e) {
            Log::error('Error loading edit page for todo ID ' . $todo->id . ': ' . $e->getMessage());

            return redirect()->route('todos.index')->with('t-error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Update the specified todo.
     *
     * @param TodoRequest $request
     * @param  Todo  $todo
     * @return RedirectResponse
     */
    public function update(TodoRequest $request, Todo $todo): RedirectResponse {
        try {
            $request->validated();

            if ($todo->due_date != Carbon::parse($request->due_date)) {
                $todo->reminder_email_sent = false;
            }

            $todo->title       = $request->title;
            $todo->email       = $request->email;
            $todo->due_date    = Carbon::parse($request->due_date);
            $todo->description = $request->description;
            $todo->save();

            $reminderTime = $todo->due_date->copy()->subMinutes(10);
            $delay        = now()->diffInSeconds($reminderTime, false);
            $delay        = $delay > 0 ? $delay : 0;

            dispatch(new SendReminderEmailJob($todo))->delay($delay);

            return redirect()->route('todos.index')->with('t-success', 'Todo updated successfully');
        } catch (Exception $e) {
            Log::error('Error updating todo: ' . $e->getMessage());
            return redirect()->back()->with('t-error', 'Something went wrong while updating the todo. Please try again.');
        }
    }

    /**
     * Change the status of the specified todo.
     *
     * @param  Todo  $todo
     * @return JsonResponse
     */
    public function status(Todo $todo): JsonResponse {
        try {
            $todo->status = $todo->status === 'active' ? 'inactive' : 'active';
            $todo->save();

            $action = $todo->status === 'active' ? 'published' : 'unpublished';

            return Helper::jsonResponse(
                true,
                $action === 'published' ? 'Published Successfully.' : 'Unpublished Successfully.',
                200,
                ['todo' => $todo, 'action' => $action]
            );
        } catch (Exception $e) {
            Log::error('Error changing todo status: ' . $e->getMessage());

            return Helper::jsonResponse(
                false,
                'Something went wrong while updating the status.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Remove the specified todo.
     *
     * @param  Todo  $todo
     * @return JsonResponse
     */
    public function destroy(Todo $todo): JsonResponse {
        try {
            $todo->delete();
            return Helper::jsonResponse(true, 'Deleted successfully.', 200);
        } catch (Exception $e) {
            Log::error('Error deleting todo: ' . $e->getMessage());
            return Helper::jsonResponse(false, 'Something went wrong while deleting the todo.', 500, $e->getMessage());
        }
    }
}
