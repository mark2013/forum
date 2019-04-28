<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DB\MySQLi;

function array_string(&$item, $key) {
    
    $item = chr(39).$item.chr(39);
    
}

/**
 * Класс образуется в результате создания таблицы, но экземпляр этого класса можно создать и самостоятельно.
 * Инкапсулирует в себе свойства и методы, необходимые для работы с полями таблицы.
 * 
 * СПИСОК МЕТОДОВ
 * public getFieldCount()
 * public getFieldsInfo()
 * protected final fieldExists()
 * public getFieldName()
 * public getFieldTable()
 * 
 * @author mkreine
 */
final class MySQLi_Table extends \DB\MySQLi\MySQLi {
    protected $table_name;
    
    protected $field_count;
    
    protected $field_types;
    
    protected $sql;
    
    /**
     * Конструктор класса
     * @param \DB\MySQLi\MySQLi $sql
     * @param type $table_name
     * @return type
     */
    public function __construct(\DB\MySQLi\MySQLi $sql, $table_name) {
        
        /**
         * Класс создаётся только при существующей таблице $table_name в результате вызова функции
         * \DB\MySQLi\create_table(). Поэтому здесь, на всякий случай, проверяем существование таблицы.
         */
            
            $this->sql  =   $sql;
            $this->table_name   =   $table_name;
            return $this->table_name;
    }
    
    /**
     * Возвращает кол-во полей в таблице
     * @return int
     */
    public function getFieldCount():int {
        
        if (is_null($this->table_name)) {
            return 0;
        }
        
        $this->query($this->sql->connection_id, "SHOW FIELDS FROM ".$this->table_name);
        
        $fields = $this->preanalyze();
        
        $i = 0;
        foreach ($fields as $value) {
            $i++;
        }
        
        return $i;
    }
    
    /**
     * Возвращает информацию обо всех полях из данной таблицы
     * @param type $table_name
     * @return array
     */
    public function getFieldsInfo($table_name = ''):array {
        
        if (empty($table_name)) {
            $table_name = $this->table_name;
        }
        
        $query = "SHOW COLUMNS FROM ".$table_name;
        $this->query($this->sql->connection_id, $query);
        $fields = $this->preanalyze();
        $field = array();
        foreach ($fields as $value) {
            $field['name'][] = $value[0];
            $field['type'][] = $value[1];
            $field['null'][] = $value[2];
            $field['default'][] = $value[3];
            $field['extra'][] = $value[4];
            
        }
        
        return $field;
                
    }
    
    protected final function fieldExists(int $field_id):boolean {
        $field_count = $this->getFieldCount();
        if ($field_id > $field_count) {
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Возвращает имя поля
     * @param int $field_id
     * @return string
     */
    public function getFieldName(int $field_id):string {
        if (!$this->fieldExists($field_id)) {
            return false;
        }
        
        $field = mysqli_fetch_field_direct($this->sql->query_id, $field_id);
        if ($field) {
            return $field->name;
        }
    }
    
    public function getFieldTable(int $field_id):string {
         if (!$this->fieldExists($field_id)) {
            return false;
        }
        
        $field = mysqli_fetch_field_direct($this->sql->query_id, $field_id);
        if ($field) {
            return $field->table;
        }
    }
    
    
    public function insert (array $fields, array &$values) {
           
          \array_walk($values, "\DB\MySQLi\array_string");
                     
          
           if ($this->wasError($this->sql->getConnectionID())) {
               $this->getError($this->sql->getConnectionID());
               return false;
           }
                   
           else {
               $query = "INSERT INTO ".$this->table_name."(".implode(",", $fields).") VALUES (".implode(",", $values).");";
               $this->query($this->sql->getConnectionID(), $query);
               return $this->getLastID($this->sql->getConnectionID());
           }
           
         
       }
    
       /**
        * Формирует строку к базе
        * @param array $fields
        * @param array $where
        * @param type $type_logic
        * @return string
        */
    public function simple_select (array $fields, array $where, $type_logic = "AND"):string {
        
        if (empty($fields)) {
            $query = "SELECT * FROM ".$this->table_name;
        }
        
        else {
            $query = "SELECT ".implode(",", $fields)." FROM ".$this->table_name;
        }
        
        if (!empty($where)) {
            
            $query .= " WHERE ";
            $i = 0;
            $cnt = count($where);
            foreach ($where as $key => $value) {
                
                $query.=$key."=".$value;
                $i++;
                if ($i !== $count) {
                    $query .= $type_logic;
                }
                else {
                    $query .= ";";
                }
            }
        }
        
        else {
            $query .= ";";
        }
        
        
        return $query;
        
        
    }
          
}
