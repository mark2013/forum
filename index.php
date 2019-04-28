<?php


require($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");

/*$query = "SELECT forum_name FROM forums WHERE forum_id = ? AND p_forum_id = ?";
$forum_id = 2;
$p_forum_id = 1;
$sql->setQuery($query);
$sql_query = $sql->prepare($sql->getConnectionID());
$sql_query->bind_param("ii", $forum_id, $p_forum_id);
$sql_query->execute();
$sql_query->bind_result($name);
$sql_query->fetch();

echo $name;*/

$forum = new Forum(1);

$m['group_id'] = 1;
$m['permission_id'] = 4;

insert($m, false);