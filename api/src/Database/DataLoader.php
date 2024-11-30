<?php
namespace Kc\CourseCatalog\Database;

class DataLoader
{
    private \PDO $db;
    private string $categoriesFile;
    private string $coursesFile;

    public function __construct()
    {
        $this->db = Connection::getInstance();

        // Uses absolute path to ensure correct file location inside the docker container (solved thankfully)
        $this->categoriesFile = '/var/www/html/data/categories.json';
        $this->coursesFile = '/var/www/html/data/course_list.json';
    }

    public function loadAll()
    {
        try {
            // Explicitly reset and get a fresh connection
            Connection::resetInstance();
            $this->db = Connection::getInstance();

            // Add more detailed logging
            error_log('Starting data load process');

            // Verify database connection
            $this->verifyDatabaseConnection();

            // Start transaction with explicit isolation level
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->db->beginTransaction();

            // Disable foreign key checks
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');

            // Log each major step
            error_log('Clearing tables');
            $this->clearTables();

            error_log('Loading categories');
            $this->loadCategories();

            error_log('Loading courses');
            $this->loadCourses();

            // Re-enable foreign key checks
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');

            // Commit transaction
            $this->db->commit();
            error_log('Data load completed successfully');

            echo "Data loaded successfully!\n";
        } catch (\PDOException $e) {
            // Comprehensive error logging
            error_log('PDO Exception during data load: ' . $e->getMessage());
            error_log('Error Code: ' . $e->getCode());

            // Always attempt rollback
            try {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
            } catch (\PDOException $rollbackException) {
                error_log('Rollback failed: ' . $rollbackException->getMessage());
            }

            // Ensure foreign key checks are re-enabled
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');

            throw $e;
        } catch (\Exception $e) {
            error_log('General Exception during data load: ' . $e->getMessage());

            // Always attempt rollback
            try {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
            } catch (\PDOException $rollbackException) {
                error_log('Rollback failed: ' . $rollbackException->getMessage());
            }

            // Ensure foreign key checks are re-enabled
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');

            throw $e;
        }
    }

    private function verifyDatabaseConnection()
    {
        try {
            $stmt = $this->db->query('SELECT 1');
            $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log('Database connection verification failed: ' . $e->getMessage());
            throw new \Exception('Unable to verify database connection');
        }
    }

    private function loadCategories()
    {
        $categoriesJson = file_get_contents($this->categoriesFile);
        $categories = json_decode($categoriesJson, true);

        if ($categories === null) {
            throw new \Exception("Failed to parse categories JSON: " . json_last_error_msg());
        }

        $stmt = $this->db->prepare('
            INSERT INTO categories (id, name, parent_id) 
            VALUES (:id, :name, :parent_id)
        ');

        foreach ($categories as $category) {
            $stmt->execute([
                'id' => $category['id'],
                'name' => $category['name'],
                'parent_id' => $category['parent'] ?? null
            ]);
        }
    }

    private function loadCourses()
    {
        $coursesJson = file_get_contents($this->coursesFile);
        $courses = json_decode($coursesJson, true);

        if ($courses === null) {
            throw new \Exception("Failed to parse courses JSON: " . json_last_error_msg());
        }

        $stmt = $this->db->prepare('
        INSERT INTO courses (id, name, description, image_preview, category_id)
        VALUES (:id, :name, :description, :image_preview, :category_id)
    ');

        foreach ($courses as $course) {
            $stmt->execute([
                'id' => $course['course_id'],
                'name' => $course['title'],
                'description' => $course['description'],
                'image_preview' => $course['image_preview'] ?? null, // Uses null if the field is missing
                'category_id' => $course['category_id']
            ]);
        }
    }

    private function clearTables()
    {
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->exec('TRUNCATE TABLE courses');
        $this->db->exec('TRUNCATE TABLE categories');
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}