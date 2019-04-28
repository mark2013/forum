<?php

/**
 * Функции, управляющие разрешениями
 * Содержание файла
 *
 * generate_allowance_string(bool $allowed, $time):string;
 * allowed(int $group_id = 0, int $user_id = 0, int $permission_id):bool;
 * permission_exists(int $permission_id):bool;
 * permission_id_to_string(int $permission_id):string;
 * permission_string_to_id(string $permission):int;
 * insert(array $data, bool $allowed):bool;
 * update_allowance(int $group_id = 0, int $user_id = 0, int $permission_id, bool $allowed):bool;
*/ 

/**
 * Генерирует строку в зависимости от условия: разрешено или запрещено
 * Строка генерируется всё время разная, за счёт использования текущей временной метки;
 * в каждом случае она будет своя, таким образом для двух разных пользователей никогда
 * не будет двух одинаковых строк
 *
 * @param bool $allowed Разрешено или запрещено действие
 * @return string Захешированная строка
*/ 
function generate_allowance_string(bool $allowed, $time):string
{
	global $config;
	if ($allowed)
	{
		$string = "permitted".$time;
	}
	else
	{
		$string = "not_permitted".$time;
	}
	
	$hashed = hash($config['crypt']['algo'], $string);
	return $hashed;
}

/**
 * Проверяем, действительно ли разрешено данное действие данному пользователю
 * Проверка основана на очень простом действии: мы не можем расшифровать строку обратно,
 * но мы знаем время, когда это было зашифровано. Вытаскиваем эти два значения из
 * базы данных. Далее составляем строку, как будто бы пользователю ЭТО разрешено,
 * шифруем её и сравниваем с той, которую мы вытащили из базы. Если результат совпадет - 
 * возвращаем true. В противном случае очевидно, что здесь у пользователя / группы нет разрешений
 * - возвращаем false.
 *
 * @param int $group_id = 0 - ID группы, если не передано - то 0
 * @param int $user_id = 0 - ID пользователя, если не передано - то 0
 * @param int $permission_id - ID разрешения
 * @return bool
*/ 
function allowed(int $group_id = 0, int $user_id = 0, int $permission_id):bool
{
	if (empty($group_id) && empty($user_id))
	{
		return false;
	}
	
	if (!permission_exists($permission_id))
	{
		return false;
	}
	
	global $sql, $config;
	
	if (!empty($user_id))
	{
		$query = "SELECT is_permitted, time_recorded FROM ".GROUP_PERMISSIONS_TABLE." WHERE user_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->bind_result($result, $time);
		$stmt->fetch();
		
		$try = "permitted".$time;
		$try_hashed = hash($config['crypt']['algo'], $try);
		
		if ($try_hashed == $result)
		{
			return true;
		}
		
		else
		{
			return false;
		}
	}
	
	
	else if (!empty($group_id))
	{
		$query = "SELECT is_permitted, time_recorded FROM ".GROUP_PERMISSIONS_TABLE." WHERE group_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("i", $group_id);
		$stmt->execute();
		$stmt->bind_result($result, $time);
		$stmt->fetch();
		
		$try = "permitted".$time;
		$try_hashed = hash($config['crypt']['algo'], $try);
		
		if ($try_hashed == $result)
		{
			return true;
		}
		
		else
		{
			return false;
		}
	}
}

/**
 * Проверяет, существует ли разрешение с данным номером
 *
 * @param int $permission_id ID разрешения, существование которого нужно проверить
 * @return bool
*/ 
function permission_exists(int $permission_id):bool
{
	global $sql;
	$query = "SELECT permission_id FROM ".PERMISSIONS_TABLE." WHERE permission_id = ?";
	$sql->setQuery($query);
	$stmt = $sql->prepare($sql->getConnectionID());
	$stmt->bind_param("i", $permission_id);
	$stmt->execute();
	$stmt->store_result();
	
	$rows = $stmt->num_rows;
	if ($rows < 1)
	{
		return false;
	}
	
	else
	{
		return true;
	}
}


/**
 * Функция преобразует ID разрешения в его строковое представление. Возвращает пустую строку, если разрешения с данным ID не существует
 *
 * @param int $permission_id ID разрешения, для которого нужно получить его имя
 * @return string
*/ 
function permission_id_to_string(int $permission_id):string
{
	if (!permission_exists($permission_id))
	{
		return '';
	}
	
	global $sql;
	$query = "SELECT permission FROM ".PERMISSIONS_TABLE." WHERE permission_id = ?";
	$sql->setQuery($query);
	$stmt = $sql->prepare($sql->getConnectionID());
	$stmt->bind_param("i", $permission_id);
	$stmt->execute();
	
	$stmt->bind_result($permission_name);
	$stmt->fetch();
	
	return $permission_name;
}

/**
 * Функция преобразует строковое представление разрешения в его ID. Возвращает 0, если разрешения с заданным именем не существует
 * 
 * @param string $oermission Строковое представление разрешения, для которого требуется найти его ID
 * @return int
*/ 
function permission_string_to_id(string $permission):int
{
	global $sql;
	$query = "SELECY permission_if FROM ".PERMISSIONS_TABLE." WHERE permission = ?";
	$p = filter_var($permission, FILTER_SANITIZE_STRING);
	$sql->setQuery($query);
	$stmt = $sql->prepare($sql->getConnectionID());
	$stmt->bind_param("s", $p);
	
	$stmt->execute();
	$stmt->store_result();
	
	$rows = $stmt->num_rows;
	
	if ($rows < 1)
	{
		return 0;
	}
	
	else
	{
		$stmt->bind_result($permission_id);
		$stmt->fetch();
	}
	
	return $permission_id;
}

/**
 * Функция вставляет строку разрешений. Ввиду того, что элемент, по которому видно, разрешено пользователю что-либо, или запрещено,
 * шифруется специальным образом, простая вставка такого элемента в текстовом виде невозможна, и должна выполняться из скрипта.
 * Функция принимает на вход массив, в котором указаны требуемые параметры, а также логическое значение, принимающее соответствующее
 * значение в зависимости от того, разрешено пользователю данное действие, или запрещено
 *
 * @param array $data - Массив данных
 * @param bool $allowed - флаг разрешения или запрета
 * @return bool
*/ 
function insert(array $data, bool $allowed):bool
{
	global $sql;

	$user_id	= isset($data['user_id']) ? $data['user_id'] : 0;
	$group_id	= isset($data['group_id']) ? $data['group_id'] : 0;
	$permission_id  = isset($data['permission_id']) ? $data['permission_id'] : 0;
	
	/**
	 * Если не передан ID пользователя, разрешение действует для группы
	 * Если не передан ID группы, разрешение действует для пользователя
	 * Одновременно два эти параметра не могут быть равными 0
	*/ 
	if (empty($user_id) && empty($group_id))
	{
		return false;
	}
	
	//если $data['permission_id'] = 0 - тоже выходим, нечего здесь делать :)
	if (empty($permission_id))
	{
		return false;
	}
	
	/**
	 * Если передан ID пользователя, нужно убедиться в том, что такой пользователь существует
	*/ 
	if ($user_id > 0)
	{
	    $query = "SELECT user_id FROM ".USERS_TABLE." WHERE user_id = ?";
	    $sql->setQuery($query);
	    $stmt = $sql->prepare($sql->getConnectionID());
	    $stmt->bind_param("i", $user_id);
	    $stmt->execute();
	    $stmt->store_result();
	    
	    $rows = $stmt->num_rows;
	    
	    //если такого пользователя нет - возвращаем false
	    if ($rows < 1)
	    {
		    $stmt->close();
		    return false;
	    }
	    
	    //иначе - начинаем процесс вставки значений
	    else
	    {
		$query = "INSERT INTO ".GROUP_PERMISSIONS_TABLE." (group_id, user_id, permission_id, is_permitted, time_recorded) VALUES (?, ?, ?, ?, ?)";
		$sql->setQuery($query);
		$group_id = 0;
		$time_recorded = time();
		$allowed_string = generate_allowance_string($allowed, $time_recorded);
		$stmt = $sql->prepare($sql->getConnectionID());
		
		//связываем параметры
		$stmt->bind_param("iiisi", $group_id, $user_id, $permission_id, $allowed_string, $time_recorded);
		echo $time_recorded;
		$stmt->execute();
		$rows = mysqli_affected_rows($sql->getConnectionID());
		if ($rows > 0)
		{
			return true;
		}
		
		else
		{
			return false;
		}
	    }
	}
	
	else if ($group_id > 0)
	{
		$query = "SELECT group_id FROM ".GROUPS_TABLE." WHERE group_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("i", $group_id);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->num_rows;
		if ($rows < 1)
		{
			$stmt->close();
			return false;
		}
		
		else
		{
			$query = "INSERT INTO ".GROUP_PERMISSIONS_TABLE." (group_id, user_id, permission_id, is_permitted, time_recorded) VALUES (?, ?, ?, ?, ?)";
			$sql->setQuery($query);
			$user_id = 0;
			$time_recorded = time();
			$allowed_string = generate_allowance_string($allowed, $time_recorded);
			$stmt = $sql->prepare($sql->getConnectionID());
			
			$stmt->bind_param("iiisi", $group_id, $user_id, $permission_id, $allowed_string, $time_recorded);
			$stmt->execute();
			$rows = mysqli_affected_rows($sql->getConnectionID());
			if ($rows > 0)
			{
				return true;
			}
			
			else
			{
				return false;
			}
		}
	}
	
}

/**
 * Обновляет конкретное разрешение для заданного пользователя или группы. Несмотря на то, что оба первых параметра
 * являются необязательными, они не могут быть опущены одновременно, и это запрограммировано в коде.
 * Возвращает true, если обновление удалось, false - если в процессе произошла какая-то ошибка.
 *
 * @param int $group_id = 0 - ID группы, если применимо
 * @param int $user_id = 0  - ID пользователя, если применимо
 * @param int $permission_id - ID разрешения, которое требуется обновить
 * @return bool
*/ 
function update_allowance(int $group_id = 0, int $user_id = 0, int $permission_id, bool $allowed):bool
{
	/**
	 * Не допускаем одновременно двух НЕ переданных параметров
	*/ 
	if (empty($user_id) && empty($group_id))
	{
		return false;
	}
	
	/**
	 * Если не существует разрешения с таким номером - тоже делать нечего
	*/ 
	if (!permission_exists($permission_id))
	{
		return false;
	}
	//генерируем строку для последующего обновления базы
	$time_recorded = time();
	$allow_string = generate_allowance_string($allowed, $time_recorded);
	
	global $sql;
	
	if (!empty($user_id))
	{
		$query = "UPDATE ".GROUP_PERMISSIONS_TABLE." SET is_permitted = ?, time_recorded = ? WHERE user_id = ? AND permission_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("siii", $allow_string, $time_recorded, $user_id, $permission_id);
		$stmt->execute();
		$rows = mysqli_affected_rows($sql->getConnectionID());
		
		if ($rows > 0)
		{
			return true;
		}
		
		else
		{
			return false;
		}
	}
	
	else if (!empty($group_id))
	{
		$query = "UPDATE ".GROUP_PERMISSIONS_TABLE." SET is_permitted = ?, time_recorded = ? WHERE group_id = ? AND permission_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("siii", $allow_string, $time_recorded, $group_id, $permission_id);
		$stmt->execute();
		$rows = mysqli_affected_rows($sql->getConnectionID());
		
		if ($rows > 0)
		{
			return true;
		}
		
		else
		{
			return false;
		}
	}
}


function delete_allowance(int $group_id = 0, int $user_id = 0, int $permission_id)
{
	if (empty($group_id) && empty($user_id))
	{
		return false;
	}
	
	if (!permission_exists($permission_id))
	{
		return false;
	}
	
	global $sql;
	
	if (!empty($user_id))
	{
		$query = "DELETE FROM ".GROUP_PERMISSIONS_TABLE." WHERE user_id = ? AND permission_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("ii", $user_id, $permission_id);
		$stmt->execute();
		$rows = mysqli_affected_rows($sql->getConnectionID());
		
		if ($rows < 1)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	else if (!empty($group_id))
	{
		$query = "DELETE FROM ".GROUP_PERMISSIONS_TABLE." WHERE group_id = ? AND permission_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("ii", $group_id, $permission_id);
		$stmt->execute();
		$rows = mysqli_affected_rows($sql->getConnectionID());
		
		if ($rows < 1)
		{
			return false;
		}
		
		else
		{
			return true;
		}
	}
}

?>