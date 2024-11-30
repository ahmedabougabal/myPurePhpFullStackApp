<?php
namespace Kc\CourseCatalog\Repositories;

use Kc\CourseCatalog\Database\Connection;
class CourseRepository
{
    public function findAll(){
        $stmt = Connection::getInstance()->query("SELECT * FROM courses");
        return $stmt->fetchAll(); // PDO::FETCHESBOTH BY DEFAULT (NUMERIC AND ASSOCIATIVE ARRAYS)
    }
    public function findById($id){
        $stmt = Connection::getInstance()->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute(['id'=> $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC); // fetches only associative arrays
    }
}