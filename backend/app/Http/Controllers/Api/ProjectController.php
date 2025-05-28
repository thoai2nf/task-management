<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'search', 'sort_by', 'sort_direction', 'per_page']);
            $projects = $this->projectService->getAllByUser(auth()->id(), $filters);

            return response()->json([
                'success' => true,
                'data' => $projects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy danh sách dự án thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateProjectRequest $request): JsonResponse
    {
        try {
            $project = $this->projectService->create($request->validated(), auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Tạo dự án thành công',
                'data' => $project
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo dự án thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $project = $this->projectService->findByIdAndUser($id, auth()->id());

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dự án không tồn tại'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $project
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy thông tin dự án thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        try {
            $project = $this->projectService->update($id, $request->validated(), auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật dự án thành công',
                'data' => $project
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật dự án thất bại',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->projectService->delete($id, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Xóa dự án thành công'
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Xóa dự án thất bại',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
}
