<?php

class Forum {

	/**
	 * Forum ID
	 * @var integer
	*/
	protected $forum_id;
	
	/**
	 * Количество тем в форуме
	 * @var integer
	*/
	protected $topics_count;
	
	/**
	 * Количество сообщений
	 * @var integer
	*/
	protected $messages_count;
	
	/**
	 * Количество вложений
	 * @var integer
	*/
	protected $attachments_count;
	
	/**
	 * Форум закрыт?
	 * @var boolean
	*/
	protected $is_locked;
	
	/**
	 * Проверяет, существует ли форум. Это защищенная функция, которая вызывается в конструкторе класса. Попытка создать объект класса с неверным ID форума приведет к ошибке
	 * 
	 * @param int $forum_id ID форума
	 * @return bool
	*/
	protected function forum_exists (int $forum_id):bool
	{
		global $sql;
		
		$query = "SELECT forum_id FROM ".FORUMS_TABLE." WHERE forum_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("i", $this->forum_id);
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
	 * Вытаскивает кол-во тем, принадлежащих данному форуму
	*/
	protected function get_topics_count ():int
	{
		global $sql;
		$query = "SELECT COUNT(topic_id) AS topics_count FROM ".TOPICS_TABLE." WHERE p_forum_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("i", $this->forum_id);
		$stmt->execute();
		
		$stmt->bind_result($topics_count);
		$stmt->fetch();
		
		$stmt->close();
		
		return $topics_count;
	} 
	
	/**
	 * Вытаскивает кол-во сообщений по данному форуму
	*/
	protected function get_messages_count():int
	{
		global $sql;
		$query = "SELECT COUNT(post_id) AS posts_count FROM ".POSTS_TABLE." WHERE p_forum_id = ?";
		$sql->setQuery($query);
		$stmt = $sql->prepare($sql->getConnectionID());
		$stmt->bind_param("i", $this->forum_id);
		$stmt->execute();
		
		$stmt->bind_result($messages_count);
		$stmt->fetch();
		
		$stmt->close();
		
		return $messages_count;
	}
	
	/**
	 * Форум закрыт?
	*/
	protected function is_locked():bool
	{
		global $sql;
		$query = "SELECT is_locked FROM ".FORUMS_TABLE." WHERE forum_id = '".$this->forum_id."'";
		$sql->query($sql->getConnectionID(), $query);
		$row = $sql->getRow();
		
		if ($row[0] == '0')
		{
			return false;
		}
		
		else
		{
			return true;
		}
	}
	
	public function __construct(int $forum_id)
	{
		if (!$this->forum_exists($forum_id))
		{
			return false;
		}
		
		$this->forum_id		=	$forum_id;
		$this->topics_count	=	$this->get_topics_count();
		$this->messages_count	=	$this->get_messages_count();
		$this->is_locked	=	$this->is_locked();
	}

	public function lock_forum():bool
	{
		if (!$this->is_locked())
		{
			global $sql;
			$query = "UPDATE ".FORUMS_TABLE." SET is_locked = 1 WHERE forum_id = ?";
			$sql->setQuery($query);
			$stmt = $sql->prepare($sql->getConnectionID());
			$stmt->bind_param("i", $this->forum_id);
			$stmt->execute();
			if (!$sql->wasError($sql->getConnectionID()))
			{
				return true;
			}
			else
			{
				echo $sql->getError();
				return false;
			}
		}
		
		else
		{
		    return false;
		}
	}
	
	public function unlock_forum():bool
	{
		if ($this->is_locked())
		{
			global $sql;
			$query = "UPDATE ".FORUMS_TABLE." SET is_locked = 0 WHERE forum_id = ?";
			$sql->setQuery($query);
			$stmt = $sql->prepare($sql->getConnectionID());
			$stmt->bind_param("i", $this->forum_id);
			$stmt->execute();
			
			if (!$sql->wasError($sql->getConnectionID()))
			{
				return true;
			}
			
			else
			{
				return false;
			}
		}
		
		else
		{
			return false;
		}
	}
	
	
	public function messages_count()
	{
		return $this->messages_count;
	}
	
	public function topics_count()
	{
		return $this->topics_count;
	}

}
?>
