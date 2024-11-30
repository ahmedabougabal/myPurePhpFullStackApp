<?php
namespace Kc\CourseCatalog\Repositories;

use Kc\CourseCatalog\Database\Connection;
class CategoryRepository
{
    public function findAll(){
        $stmt = Connection::getInstance()->query("SELECT * FROM categories");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function findById($id){
        $stmt = Connection::getInstance()->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}