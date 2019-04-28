/**
 * Table: users
*/
DROP TABLE IF EXISTS users;
CREATE TABLE users (
        user_id int unsigned not null auto_increment primary key,
        group_id int unsigned not null,
        user_rank int unsigned not null default 0,
        login varchar(15) character set utf8 not null,
        password varchar(100) character set utf8 not null,
        password_salt varchar(50) character set utf8,
        email varchar(70) character set utf8 not null default '',
        real_name varchar(30) character set utf8,
        date_registered int unsigned not null,
        last_post_id int unsigned not null default 0,
        last_topic_id int unsigned not null default 0,
        is_banned tinyint(1) default 0,
        post_count int unsigned not null default 0,
        topics_count int unsigned not null default 0,
        KEY group_id (group_id),
        UNIQUE KEY login (login)
);

/**
 * Table: user_ranks
*/
DROP TABLE IF EXISTS user_ranks;
CREATE TABLE user_ranks (
        rank_id int unsigned not null auto_increment primary key,
        rank_name varchar(50) character set utf8 not null,
        rank_img_path varchar(150) character set utf8 not null default '',
        rank_min_posts int not null default 0,
        rank_min_topics int not null default 0
);

/**
 * Table: groups
*/
DROP TABLE IF EXISTS groups;
CREATE TABLE groups (
        group_id int unsigned not null auto_increment primary key,
        group_name varchar(20) character set utf8 not null,
        group_description text character set utf8 not null default '',
        users_count int unsigned not null default 0,
        UNIQUE KEY u_group (group_name)
);

/**
 * Table: permissions
*/
DROP TABLE IF EXISTS permissions;
CREATE TABLE permissions (
        permission_id int unsigned not null auto_increment primary key,
        permission varchar(50) character set utf8 not null,
        UNIQUE KEY perm (permission)
);

DROP TABLE IF EXISTS permissions_objects;

DROP TABLE IF EXISTS group_permissions;
CREATE TABLE group_permissions (
        g_perm_id int unsigned not null auto_increment primary key,
        group_id int unsigned not null default 0,
        user_id int unsigned not null default 0,
        permission_id int unsigned not null,
        is_permitted varchar(300) character set utf8,
        time_recorded time
);


/**
 * Table: forums
*/
DROP TABLE IF EXISTS forums;
CREATE TABLE forums (
        forum_id int unsigned not null auto_increment primary key,
        p_forum_id int unsigned not null default 0,
        forum_name varchar(80) character set utf8 not null,
        forum_description text character set utf8 default '',
        is_locked tinyint(1) default 0,
        topics_count int unsigned default 0,
        posts_count int unsigned default 0,
        last_topic_id int unsigned default 0,
        last_post_id int unsigned default 0
);

/**
 * Table: topics
*/
DROP TABLE IF EXISTS topics;
CREATE TABLE topics (
        topic_id int unsigned not null auto_increment primary key,
        p_forum_id int unsigned not null,
        topic_start_post_id int unsigned not null default 0,
        topic_start_poster_id int unsigned not null default 0,
        posts_count int unsigned not null default 1,
        topic_status int unsigned not null
);

/**
 * Table: topic_statuses
*/
DROP TABLE IF EXISTS topic_statuses;
CREATE TABLE topic_statuses (
        topic_status_id int unsigned not null auto_increment primary key,
        topic_status_name varchar(80) character set utf8 not null
);

/**
 * Table: posts
*/
DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
        post_id int unsigned not null auto_increment primary key,
        poster_id int unsigned not null,
        p_topic_id int unsigned not null,
        p_forum_id int unsigned not null,
        html_allowed tinyint(1) not null default 0,
        bbcode_allowed tinyint(1) not null default 1,
        post_topic varchar(100) character set utf8 not null,
        post_text text character set utf8 not null,
        post_date_posted int unsigned not null,
        post_attachments_count int unsigned not null default 0,
        post_inline_attachments_count int unsigned not null default 0
);

/**
 * Table: post_attachments
*/
DROP TABLE IF EXISTS post_attachments;
CREATE TABLE post_attachments (
        attachment_id int unsigned not null auto_increment primary key,
        post_id int unsigned not null,
        orig_file_name varchar(150) character set utf8 not null,
        transformed_file_name varchar(150) character set utf8 not null,
        attachment_extension varchar(5) character set utf8 not null,
        attachment_group int unsigned not null,
        inline_attachment tinyint(1) not null default 0
);

/**
 * Table: attachment_groups
*/
DROP TABLE IF EXISTS attachment_groups;
CREATE TABLE attachment_groups (
        attachment_group_id int unsigned not null auto_increment primary key,
        attachment_group_name varchar(100) character set utf8 not null
);

/**
 * Table: attachment_extensions
*/
DROP TABLE IF EXISTS attachment_extensions;
CREATE TABLE attachment_extensions (
        ext_id int unsigned not null auto_increment primary key,
        ext_group_id int unsigned not null,
        ext varchar(10) character set utf8 not null
);

/**
 * Table: sessions
*/
DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions (
        session_auto_id int unsigned not null auto_increment primary key,
        session_id varchar(150) character set utf8 not null,
        session_date_started varchar(100) not null,
        session_ip varchar(150) not null,
        session_anonym tinyint(1) not null default 1
);