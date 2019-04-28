INSERT INTO permissions (permission) VALUES ('create_post');
INSERT INTO permissions (permission) VALUES ('create_topic');
INSERT INTO permissions (permission) VALUES ('create_forum');
INSERT INTO permissions (permission) VALUES ('create_bbcode');
INSERT INTO permissions (permission) VALUES ('edit_own_post');
INSERT INTO permissions (permission) VALUES ('edit_any_post');
INSERT INTO permissions (permission) VALUES ('edit_own_topic');
INSERT INTO permissions (permission) VALUES ('edit_any_post');
INSERT INTO permissions (permission) VALUES ('delete_own_post');
INSERT INTO permissions (permission) VALUES ('delete_any_post');
INSERT INTO permissions (permission) VALUES ('delete_own_topic');
INSERT INTO permissions (permission) VALUES ('delete_any_topic');
INSERT INTO permissions (permission) VALUES ('login');
INSERT INTO permissions (permission) VALUES ('view_admin');
INSERT INTO permissions (permission) VALUES ('ban_users');
INSERT INTO permissions (permission) VALUES ('unban_users');
INSERT INTO permissions (permission) VALUES ('post_complaints');
INSERT INTO permissions (permission) VALUES ('access_admin_part');
INSERT INTO permissions (permission) VALUES ('moderate_own_topic');
INSERT INTO permissions (permission) VALUES ('moderate_any_topic');
INSERT INTO permissions (permission) VALUES ('moderate_forum');
INSERT INTO permissions (permission) VALUES ('global_moderator');

INSERT INTO permissions_objects (permission_object_name) VALUES ('forum');
INSERT INTO permissions_objects (permission_object_name) VALUES ('topic');
INSERT INTO permissions_objects (permission_object_name) VALUES ('post');


INSERT INTO groups (group_name) VALUES ('Аноним');
INSERT INTO groups (group_name) VALUES ('Зарегистрированный пользователь');
INSERT INTO groups (group_name) VALUES ('Модератор');
INSERT INTO groups (group_name) VALUES ('Администратор');
INSERT INTO groups (group_name) VALUES ('Заблокированные');


INSERT INTO forums (forum_name) VALUES ('Первый форум 1');
INSERT INTO forums (p_forum_id, forum_name) VALUES (1, 'Внутри первого форума 1.1.');
INSERT INTO forums (p_forum_id, forum_name) VALUES (1, 'Внутри первого форума 1.2.');
INSERT INTO forums (p_forum_id, forum_name) VALUES (1, 'Внутри первого форума 1.3.');
INSERT INTO forums (forum_name) VALUES ('Второй форум 2');
INSERT INTO forums (forum_name) VALUES ('Третий форум 3');
INSERT INTO forums (p_forum_id, forum_name) VALUES (6, 'Внутри третьего форума 3.1.');
INSERT INTO forums (p_forum_id, forum_name) VALUES (6, 'Внутри третьего форума 3.2.');
INSERT INTO forums (p_forum_id, forum_name) VALUES (6, 'Внутри третьего форума 3.3.');