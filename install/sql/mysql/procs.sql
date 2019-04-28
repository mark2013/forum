DELIMITER //

DROP FUNCTION IF EXISTS `get_topic_status`//
CREATE FUNCTION get_topic_status (topic_id int) RETURNS INTEGER
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE topic_status_id INT;
        SELECT topic_status INTO topic_status_id FROM topics WHERE topic_id = topic_id;
        RETURN topic_status_id;
END//

DROP FUNCTION IF EXISTS `user_is_banned`//
CREATE FUNCTION `user_is_banned` (idUser int) RETURNS BOOLEAN
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE banStatus INTEGER;
        SELECT is_banned INTO banStatus FROM users WHERE user_id = idUser;
            
        RETURN IF(banStatus = 0, false, true);
END//

DROP FUNCTION IF EXISTS `get_user_group_id`//
CREATE FUNCTION `get_user_group_id` (idUser int) RETURNS INTEGER
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE idGroup INTEGER;
        SELECT group_id INTO idGroup FROM users WHERE user_id = idUser;
        RETURN idGroup;
END//

DROP FUNCTION IF EXISTS `get_user_group_name`//
CREATE FUNCTION `get_user_group_name` (idUser int) RETURNS varchar(100)
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE name VARCHAR(100);
        SELECT group_name INTO name FROM groups WHERE group_id = (SELECT group_id FROM users WHERE user_id = idUser);
        RETURN name;
END//

DROP FUNCTION IF EXISTS `get_group_users_count`//
CREATE FUNCTION `get_group_users_count` (idGroup int) RETURNS INTEGER
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE uCount INTEGER;
        SELECT users_count INTO uCount FROM groups WHERE group_id = idGroup;
        RETURN uCount;
END//

DROP FUNCTION IF EXISTS `get_user_rank`//
CREATE FUNCTION `get_user_rank` (idUser int) RETURNS INTEGER
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE uRank INTEGER;
        SELECT user_rank INTO uRank FROM users WHERE user_id = idUser;
        RETURN user_rank;
END//

DROP FUNCTION IF EXISTS `get_last_post_id`//
CREATE FUNCTION `get_last_post_id` (idUser int) RETURNS INTEGER
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE uLastPost INTEGER;
        SELECT last_post_id INTO uLastPost FROM users WHERE user_id = idUser;
        RETURN uLastPost;
END//

DROP FUNCTION IF EXISTS `get_last_topic_id`//
CREATE FUNCTION `get_last_topic_id` (idUser int) RETURNS INTEGER
DETERMINISTIC READS SQL DATA
BEGIN
        DECLARE uLastTopic INTEGER;
        SELECT last_topic_id INTO uLastTopic FROM users WHERE user_id = idUser;
        RETURN uLastTopic;
END//
