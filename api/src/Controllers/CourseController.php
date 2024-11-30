<?php
namespace Kc\CourseCatalog\Controllers;
use Kc\CourseCatalog\Services\CourseService;

class CourseController
{
    private CourseService $courseService;
    public function __construct(){
        $this->courseService = new CourseService();
    }
    public function index(): array
    {
        return $this->courseService->getAllCourses();
    }
    public function show($id){
        return $this->courseService->getCourseById($id);
    }
}