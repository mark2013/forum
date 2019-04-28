<?php

error_reporting(E_ALL & ~E_NOTICE);

if (!is_array($config) || !isset($config)) {
    $config = array();
}

//стартуем сессию
session_start();

//когда стартовали?
$date_started   =   time();

/* пишем сюда 0. Это значит, что на форуме - аноним. Впоследствии, когда человек войдет на форум, здесь будет
 * id реального пользователя
 * 
 */
$_SESSION['user_id'] = 0;

//конфигурационные переменные
$config['db']['server']     =   'localhost';
$config['db']['user']       =   'root';
$config['db']['password']   =   'drp72TuKr1sE52kMs';
$config['db']['dbname']     =   'forum_php';
$config['db']['port']       =   3306;
$config['db']['persistent'] =   false;
$config['db']['layer']      =   'mysqli';
$config['session']['timeout']   =   900; //время в секундах

$config['crypt']['algo'] = 'haval256,5';
$config['crypt']['function'] = 'hash';

//ради безопасности
$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
$ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
$_SESSION['IP'] =   $ip;


//подключение необходимых файлов
require($root."/classes/DB/Database.php");
require($root."/classes/DB/".\DB\Database::$dblayer."/".\DB\Database::$dblayer.".php");
require($root."/classes/DB/".\DB\Database::$dblayer."/".\DB\Database::$dblayer."_Table.php");
require($root."/classes/User.php");
require($root."/classes/Forum.php");

require($root."/includes/functions_forums.php");
require($root."/includes/functions_permissions.php");

//инициализация необходимых классов
$layer = "\\DB\\".\DB\Database::$dblayer."\\".\DB\Database::$dblayer;

$sql = new $layer();

/**
 * Инициализируем класс пользователя в зависимости от условий: если на компьютере клиента присутствует
 * cookie с заданным именем, считаем, что на форум зашёл реальный пользователь. В противном случае - аноним
 */
if (!isset($_COOKIE['user_id'])) {
    $user = new User(0);
}

else {
    
    $user_id = (int)$_COOKIE['user_id'];
    
    //здесь проверка на случай, если данные cookie подделали злоумышленники
    if (User::exists($user_id)) {
        $user = new User($user_id);
    }     
}

?>