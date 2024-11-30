<?php
namespace Kc\CourseCatalog\Controllers;
use Kc\CourseCatalog\Services\CategoryService;
class CategoryController
{
    private CategoryService $categoryService;
    public function __construct(){
        $this->categoryService = new CategoryService();
    }
    public function index(): array
    {
        return $this->categoryService->getAllCategories();
    }
    public function Show($id){
        return $this->categoryService->getCategoryById($id);
    }
}