<?php
namespace Kc\CourseCatalog\Services;

use Kc\CourseCatalog\Repositories\CourseRepository;

class CourseService{
    private CourseRepository $courseRepository;
    public function __construct()
    {
        $this->courseRepository = new CourseRepository();
    }
    public function getAllCourses(){
        return $this->courseRepository->findAll();
    }
    public function getCourseById($id){
        return $this->courseRepository->findById($id);
    }
}