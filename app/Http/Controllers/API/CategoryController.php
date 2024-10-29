<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    protected $categoryModel;

    public function __construct(Category $category)
    {
        $this->categoryModel = $category;
    }

    public function getAllCategories(Request $request)
    {
        try {
            $limit = $request->query('limit');

            if (is_null($limit)) {
                $categories = $this->categoryModel->all();
            } else {
                $categories = $this->categoryModel->paginate($limit);
            }
            return response()->json(
                [
                    'message' => 'Category listed successfully.',
                    'data' => CategoryResource::collection($categories),
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve categories.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getCategoryById($id)
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category) {
                return response()->json(['message' => 'Category not found.'], 404);
            }

            return response()->json(
                [
                    'message' => 'Category found successfully.',
                    'data' => new CategoryResource($category),
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve category.', 'error' => $e->getMessage()], 500);
        }
    }

    public function createCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $existingCategory = $this->categoryModel->where('name', $request->name)->first();

            if ($existingCategory) {
                return response()->json(['message' => 'Category has been created.'], 404);
            }

            $category = $this->categoryModel->create([
                'name' => $request->name,
            ]);

            DB::commit();

            return response()->json(
                [
                    'message' => 'Category created successfully.',
                    'data' => new CategoryResource($category),
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to retrieve category.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateCategory(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category = $this->categoryModel->find($id);

            if (!$category) {
                return response()->json(['message' => 'Category not found.'], 404);
            }

            $existingCategory = $this->categoryModel
                ->where('name', $request->name)
                ->where('id', '!=', $id)
                ->first();

            if ($existingCategory) {
                return response()->json(['message' => 'Category has been existed.'], 404);
            }

            $category->name = $request->name;
            $category->save();

            DB::commit();

            return response()->json(
                [
                    'message' => 'Category updated successfully.',
                    'data' => new CategoryResource($category),
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to update category.', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteCategory($id)
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryModel->find($id);

            if (!$category) {
                return response()->json(['message' => 'Category not found.'], 404);
            }

            $category->delete();

            DB::commit();

            return response()->json(['message' => 'Category deleted successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to delete category.', 'error' => $e->getMessage()], 500);
        }
    }
}
