<?php
// app/Http/Controllers/Api/TaskController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {
    }

    public function index(Request $request, int $projectId): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'priority', 'search', 'sort_by', 'sort_direction']);
            $tasks = $this->taskService->getAllByProject($projectId, auth()->id(), $filters);

            return response()->json([
                'success' => true,
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Lấy danh sách nhiệm vụ thất bại',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function store(CreateTaskRequest $request, int $projectId): JsonResponse
    {
        try {
            $task = $this->taskService->create($request->validated(), $projectId, auth()->id());

            // Broadcast event for real-time updates
            broadcast(new \App\Events\TaskCreated($task));

            return response()->json([
                'success' => true,
                'message' => 'Tạo nhiệm vụ thành công',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Tạo nhiệm vụ thất bại',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $task = $this->taskService->findById($id, auth()->id());

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhiệm vụ không tồn tại'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy thông tin nhiệm vụ thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->update($id, $request->validated(), auth()->id());

            // Broadcast event for real-time updates
            Log::info("controller Task Updated task project ".$task->project_id);
//            broadcast(new \App\Events\TaskUpdated($task));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhiệm vụ thành công',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật nhiệm vụ thất bại',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $task = $this->taskService->findById($id, auth()->id());
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhiệm vụ không tồn tại'
                ], 404);
            }

            $this->taskService->delete($id, auth()->id());

            // Broadcast event for real-time updates
            broadcast(new \App\Events\TaskDeleted($task))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Xóa nhiệm vụ thành công'
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Xóa nhiệm vụ thất bại',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|integer|exists:tasks,id',
            'tasks.*.order' => 'required|integer|min:0'
        ]);

        try {
            $this->taskService->updateOrder($request->tasks, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thứ tự nhiệm vụ thành công'
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thứ tự nhiệm vụ thất bại',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
}
