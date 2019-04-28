<?php

namespace DB;

abstract class Database {
    
      
        /**
         * Имя сервера
         * @var string 
         */
	protected $server;
	
        /**
         * Имя пользователя для подключения
         * @var string
         */
	protected $user;
	
        /**
         * Пароль для подключения
         * @var string
         */
	protected $password;
	
        /**
         * Имя базы данных
         * @var string 
         */
	protected $database;
	
        /**
         * Порт подключения
         * @var integer
         */
	protected $port;
	
        /**
         * Поддерживать ли постоянное соединение
         * @var boolean 
         */
	protected $persistent;
	
        /**
         * Текст текущего запроса
         * @var string 
         */
	protected $query_text;
        
        /**
         * ID соединения
         * @var resource
         */
        protected $connection_id;
        
        /**
         * ID запроса
         * @var resource
         */
        protected $query_id;
        
        /**
         * Статическая переменная, указывающая на тип используемой СУБД
         * @var string 
         */
        public static $dblayer = 'MySQLi';
	
        /**
         * конструктор класса. Устанавливает значения полей класса. Принимает переменное кол-во параметров
         * @param type $data
         */
	public function __construct() {
		
	}
        
        	
	public function setQuery($text) {
		
		$this->query_text	=	trim($text);
                return $this->query_text;
	}
        
             
        
        /**
         * Очищает поле с текстом запроса
         */
        public function emptyQuery():void {
            $this->query_text = '';
        }
        
        public function getConnectionID() {
                return $this->connection_id;
        }
        
        public function getQueryID() {          
                return $this->query_id;
        }
        
        public function getServer():string {            
            return $this->server;
        }
        
        public function getUser():string {
            return $this->user;
        }
        
        public function getPort():integer {
            return $this->port;
        }

	public function getQueryText():string {
		return $this->query_text;
	}
        
}

?>
