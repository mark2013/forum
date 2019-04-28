<?php


function getAll()
{
	global $sql;
	$query = "SELECT * FROM forums";
	$sql->query($sql->getConnectionID(), $query);
	
	$ret = array();
	while ($line = $sql->getRow(true))
	{
		$ret[$line['p_forum_id']][] = $line;
	}
	
	return $ret;
}



function outTree($array, $parent_id, $level) 
{
     if (isset($array[$parent_id]))
      { //Если категория с таким parent_id существует
	     echo'<ul class="vyp-menu">';
             foreach ($array[$parent_id] as $value)
              {
    	    echo "<li><a href=''>" . $value['forum_name'] . "</a>";
            $level++; //Увеличиваем уровень вложености
            //Рекурсивно вызываем этот же метод, но с новым $parent_id и $level
            outTree($array, $value['forum_id'], $level);
            echo'</li>';
            $level--; //Уменьшаем уровень вложености
	      }
         echo'</ul>';
    }
}