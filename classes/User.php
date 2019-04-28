<?php


/**
 * Класс пользователя. Создаётся каждый раз при авторизации конкретного пользователя.
 * Содержит все методы, необходимые для работы с авторизованным пользователем.
 *
 * @author mkreine
 */
class User {
    /**
     * ID пользователя
     * @var integer
     * @access protected
     */
    protected $user_id;
    
    /**
     * ID сессии пользователя. Есть у каждого юзера, даже у анонима
     * @param type $id
     */
    protected $session_id;
    
    /**
     * Конструктор класса. Инициализируется с ID пользователя. 
     * @param integer $id
     */
    public function __construct($id) {
        global $layer;

        //инициализируем класс с ID пользователя        
        $this->user_id  =   (int)$id;
        
        //стартуем сессию или продолжаем старую
        session_start();
        
        //узнаем session_id
        $this->session_id   =   session_id();
        
        //пишем ID пользователя в сессию
        $_SESSION['user_id']    =   (int)$this->user_id;
    }
    
    /**
     * Проверяет существование пользователя с идентификатором $id. 
     * Возвращает false если пользователя нет, true - в противном случае
     * 
     * @param type $id ID пользователя
     * @return boolean
     * @static
     */
    public static function exists(int $id) {
        
        $query = 'SELECT user_id FROM '.USERS_TABLE.' WHERE user_id = ?';
        global $sql;
        $sql->setQuery($query);
        $stmt = $sql->prepare($sql->getConnectionID());
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        
        
        
	$rows = $stmt->num_rows;
        if ($rows < 1) {
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Осуществляет процедуру логина
     * 
     * @param array $userdata данные с которыми входим
     * @return boolean|\User
     * @static
     */
    public static function login(array $userdata) {
        
        //Если не предоставлено имя пользователя или пароль, завершаем работу
        if (!array_key_exists('user', $userdata) || !array_key_exists('pass', $userdata)) {
            return false;
        }
        
        $login = trim($userdata['user']);
        $pass  = md5($userdata['pass']);
        
        global $sql;
        //проверяем, имеется ли пользователь с такими данными    
        $query = 'SELECT user_id FROM '.USERS_TABLE.' WHERE login = ? AND password = ?';
        
        $sql->setQuery($query);
        $stmt = $sql->prepare($sql->getConnectionID());
        $stmt->bind_param("ss", $login, $pass);
        $stmt->execute();
        $stmt->store_result();
        
        $rows = $stmt->num_rows;
        
        //нет? выходим
        if (!$rows || $rows < 1) {
            return false;
        }
        
        //иначе осуществляем процедуру логина
        else {
            $stmt->bind_result($result);

            setcookie('user_id', $result);
            return new User($result);
        }
        
        $stmt->close();
        
    }
    
    public function getUserLogin():string {
        
        //если в id передан не ноль, значит зашёл не анонимный пользователь
        if ($id !== 0) {
            $query = 'SELECT login, password FROM '.USERS_TABLE.' WHERE user_id = ?';
            
            global $sql;
            $sql->setQuery($query);
            $stmt = $sql->prepare($sql->getConnectionID());
            $stmt->bind_param("i", $this->user_id);
            $stmt->execute();
            $stmt->store_result();
            $rows = $stmt->num_rows;
            if ($rows > 0) {
                $stmt->bind_result($user_login);
                return $user_login;
            }
            
            $stmt->close();
        }
        
        else
        {
    	    return '';
        }
        
    }
        
    public function getUserGroup($id = -1) {
        
        if ($id == -1) {
            $id = $this->user_id;
        }
        
        $query = "SELECT group_id FROM ".USERS_TABLE." WHERE user_id = ".(int)$id;
        $this->db->query($this->db->getConnectionId(), $query);
        
        $result = $this->db->getRow();
        return (int)$result['group_id'];
    }
    
    public function get_user_date_registered() {
        
        if (!self::exists($this->id)) {
            return false;
        }
        
        $query = "SELECT date_registered FROM ".USERS_TABLE." WHERE user_id = ".(int)$this->id;
        $this->db->query($this->db->getConnectionID(), $query);
        $result = $this->db->getRow();
        
        $real_date = date("d.m.Y", $result);
        return $real_date;
        
    }
}
