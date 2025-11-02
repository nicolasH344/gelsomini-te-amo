<?php
namespace App\Models;

use App\Config\Database;
use PDO;

abstract class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function findAll($limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($limit) {
            $sql .= " LIMIT ?";
            if ($offset) {
                $sql .= " OFFSET ?";
            }
        }
        $stmt = $this->db->prepare($sql);
        $params = [];
        if ($limit) {
            $params[] = (int)$limit;
            if ($offset) {
                $params[] = (int)$offset;
            }
        }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $fields = array_keys($data);
        $fieldList = implode(',', array_map(function($field) {
            return preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        }, $fields));
        $placeholders = ':' . implode(', :', $fields);
        
        $sql = "INSERT INTO {$this->table} ({$fieldList}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', array_map(function($field) {
            return preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        }, $fields)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        
        $values = array_values($data);
        $values[] = (int)$id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>