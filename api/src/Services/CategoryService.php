<?php
namespace Kc\CourseCatalog\Services;

use Kc\CourseCatalog\Repositories\CategoryRepository;

class CategoryService{
    private CategoryRepository $categoryRepository;
    public function __construct(){
        $this->categoryRepository = new CategoryRepository();
    }
    public function getAllCategories(){
        return $this->categoryRepository->findAll();
    }
    public function getCategoryById($id){
        return $this->categoryRepository->findById($id);
    }
}