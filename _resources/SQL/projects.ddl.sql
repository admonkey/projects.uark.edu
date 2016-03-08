-- TABLES --
DROP TABLE IF EXISTS Votes_History;
DROP TABLE IF EXISTS Votes;
DROP TABLE IF EXISTS Link_Groups_Content;
DROP TABLE IF EXISTS Link_Groups_Users_History;
DROP TABLE IF EXISTS Link_Groups_Users;
DROP TABLE IF EXISTS Content_History;
DROP TABLE IF EXISTS Content;
DROP TABLE IF EXISTS Groups_History;
DROP TABLE IF EXISTS Groups;
DROP TABLE IF EXISTS Users_History;
DROP TABLE IF EXISTS Users;

CREATE TABLE IF NOT EXISTS Users (
  email VARCHAR(30) NOT NULL UNIQUE,
  username VARCHAR(30) NOT NULL,
  profile_picture VARCHAR(200),
  private_profile BOOLEAN DEFAULT FALSE,

  user_key INT PRIMARY KEY AUTO_INCREMENT,
  user_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  user_edited_time TIMESTAMP NULL,
  user_editedby_user_key INT,
  FOREIGN KEY (user_editedby_user_key) REFERENCES Users(user_key),
  user_deleted BOOLEAN DEFAULT FALSE
);
CREATE TABLE IF NOT EXISTS Users_History (
  email VARCHAR(30) NOT NULL,
  username VARCHAR(30) NOT NULL,
  profile_picture VARCHAR(200),
  private_profile BOOLEAN NOT NULL,

  user_key INT NOT NULL,
  user_creation_time TIMESTAMP NOT NULL,
  
  user_edited_time TIMESTAMP NULL,
  user_editedby_user_key INT,
  FOREIGN KEY (user_editedby_user_key) REFERENCES Users(user_key),
  user_deleted BOOLEAN NOT NULL
);

CREATE TABLE IF NOT EXISTS Groups (
  group_name VARCHAR(100) NOT NULL,

  group_key INT PRIMARY KEY AUTO_INCREMENT,
  group_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  group_createdby_user_key INT NOT NULL,
  FOREIGN KEY (group_createdby_user_key) REFERENCES Users(user_key),
  
  group_edited_time TIMESTAMP NULL,
  group_editedby_user_key INT,
  FOREIGN KEY (group_editedby_user_key) REFERENCES Users(user_key),
  group_deleted BOOLEAN DEFAULT FALSE
);
CREATE TABLE IF NOT EXISTS Groups_History (
  group_name VARCHAR(100) NOT NULL,

  group_key INT NOT NULL,
  group_creation_time TIMESTAMP NOT NULL,
  group_createdby_user_key INT NOT NULL,
  FOREIGN KEY (group_createdby_user_key) REFERENCES Users(user_key),
  
  group_edited_time TIMESTAMP NULL,
  group_editedby_user_key INT,
  FOREIGN KEY (group_editedby_user_key) REFERENCES Users(user_key),
  group_deleted BOOLEAN NOT NULL
);

CREATE TABLE IF NOT EXISTS Content (
  content_title VARCHAR(100),
  content_value VARCHAR(1000),

  project_key INT,
  FOREIGN KEY (project_key) REFERENCES Content(content_key),
  thread_key INT,
  FOREIGN KEY (thread_key) REFERENCES Content(content_key),
  group_key INT,
  FOREIGN KEY (group_key) REFERENCES Groups(group_key),

  -- recursive reply-to hierarchy
  parent_content_key INT,
  FOREIGN KEY (parent_content_key) REFERENCES Content(content_key),
  has_children BOOLEAN DEFAULT FALSE,

  content_key INT PRIMARY KEY AUTO_INCREMENT,
  content_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  content_createdby_user_key INT NOT NULL,
  FOREIGN KEY (content_createdby_user_key) REFERENCES Users(user_key),

  content_edited_time TIMESTAMP NULL,
  content_editedby_user_key INT,
  FOREIGN KEY (content_editedby_user_key) REFERENCES Users(user_key),
  content_deleted BOOLEAN DEFAULT FALSE
);
CREATE TABLE IF NOT EXISTS Content_History (
  content_title VARCHAR(100),
  content_value VARCHAR(1000),
  
  project_key INT,
  FOREIGN KEY (project_key) REFERENCES Content(content_key),
  thread_key INT,
  FOREIGN KEY (thread_key) REFERENCES Content(content_key),
  group_key INT NOT NULL,
  FOREIGN KEY (group_key) REFERENCES Groups(group_key),

  parent_content_key INT,
  FOREIGN KEY (parent_content_key) REFERENCES Content(content_key),
  has_children BOOLEAN DEFAULT FALSE,

  content_key INT NOT NULL,
  content_creation_time TIMESTAMP NOT NULL,
  content_createdby_user_key INT NOT NULL,
  FOREIGN KEY (content_createdby_user_key) REFERENCES Users(user_key),

  content_edited_time TIMESTAMP NULL,
  content_editedby_user_key INT,
  FOREIGN KEY (content_editedby_user_key) REFERENCES Users(user_key),
  content_deleted BOOLEAN NOT NULL
);

-- linking tables
CREATE TABLE IF NOT EXISTS Link_Groups_Users (
  group_key INT NOT NULL,
  FOREIGN KEY (group_key) REFERENCES Groups(group_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  is_admin BOOLEAN DEFAULT FALSE,

  PRIMARY KEY (group_key,user_key),
  group_user_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  group_user_createdby_user_key INT NOT NULL,
  FOREIGN KEY (group_user_createdby_user_key) REFERENCES Users(user_key),

  group_user_edited_time TIMESTAMP NULL,
  group_user_editedby_user_key INT,
  FOREIGN KEY (group_user_editedby_user_key) REFERENCES Users(user_key),
  group_user_deleted BOOLEAN DEFAULT FALSE
);
CREATE TABLE IF NOT EXISTS Link_Groups_Users_History (
  group_key INT NOT NULL,
  FOREIGN KEY (group_key) REFERENCES Groups(group_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  is_admin BOOLEAN NOT NULL,

  group_user_creation_time TIMESTAMP NOT NULL,
  group_user_createdby_user_key INT NOT NULL,
  FOREIGN KEY (group_user_createdby_user_key) REFERENCES Users(user_key),

  group_user_edited_time TIMESTAMP NULL,
  group_user_editedby_user_key INT,
  FOREIGN KEY (group_user_editedby_user_key) REFERENCES Users(user_key),
  group_user_deleted BOOLEAN NOT NULL
);

-- votes
CREATE TABLE IF NOT EXISTS Votes (
  vote_value TINYINT NOT NULL, -- downvote = -1, upvote = 1, inappropriate flag = -2
  
  content_key INT NOT NULL,
  FOREIGN KEY (content_key) REFERENCES Content(content_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  
  vote_creation_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (content_key,user_key)
);
CREATE TABLE IF NOT EXISTS Votes_History (
  vote_value TINYINT NOT NULL, -- downvote = -1, upvote = 1, inappropriate flag = -2
  
  content_key INT NOT NULL,
  FOREIGN KEY (content_key) REFERENCES Content(content_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  
  vote_creation_time TIMESTAMP NOT NULL
);

-- STORED PROCEDURES --

DROP PROCEDURE IF EXISTS login_shib_user;
DROP FUNCTION IF EXISTS create_new_content_key;
DROP FUNCTION IF EXISTS create_project;
DROP PROCEDURE IF EXISTS fetch_projects;
DROP PROCEDURE IF EXISTS fetch_children;
-- DROP PROCEDURE IF EXISTS fetch_project;
DROP FUNCTION IF EXISTS create_thread;
-- DROP PROCEDURE IF EXISTS fetch_threads;
-- DROP PROCEDURE IF EXISTS fetch_thread;
DROP FUNCTION IF EXISTS create_comment;
-- DROP PROCEDURE IF EXISTS fetch_comment;
DROP PROCEDURE IF EXISTS edit_content;
-- DROP PROCEDURE IF EXISTS delete_content;

-- TODO:
-- DROP PROCEDURE IF EXISTS create_content;

DELIMITER $$

CREATE PROCEDURE login_shib_user (
  IN p_email VARCHAR(30),
  IN p_username VARCHAR(30)
)
this_procedure:BEGIN

  DECLARE existing_user_key INT DEFAULT NULL;
  DECLARE db_username VARCHAR(30);

  SELECT user_key, username
  INTO existing_user_key, db_username
  FROM Users
  WHERE email = p_email;

  IF existing_user_key IS NOT NULL THEN
    SELECT existing_user_key AS 'user_key',
      db_username AS 'username',
      p_email AS 'email';
    LEAVE this_procedure;
  END IF;

  -- create new user record
  INSERT INTO Users (
    email,
    username
  )
  VALUES (
    p_email,
    p_username
  );
  
  SELECT LAST_INSERT_ID() AS 'user_key',
    p_username AS 'username',
    p_email AS 'email';

END $$


CREATE FUNCTION create_new_content_key (p_content_createdby_user_key INT, p_parent_content_key INT)
RETURNS INT

BEGIN

  DECLARE new_content_key INT DEFAULT NULL;
  DECLARE valid_content_createdby_user_key INT DEFAULT NULL;
  DECLARE valid_parent_content_key INT DEFAULT NULL;
  DECLARE new_group_name VARCHAR(100) DEFAULT NULL;
  DECLARE new_group_key INT DEFAULT NULL;
  
  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    RETURN NULL;
  END IF;
  
  IF p_parent_content_key IS NOT NULL THEN
    SELECT content_key
    INTO valid_parent_content_key
    FROM Content
    WHERE content_key = p_parent_content_key
      AND content_deleted = FALSE;
    IF valid_parent_content_key IS NULL THEN
      RETURN NULL;
    END IF;
  END IF;

  INSERT INTO Content (content_createdby_user_key,parent_content_key)
  VALUES (p_content_createdby_user_key,valid_parent_content_key);
  SET new_content_key = LAST_INSERT_ID();
  
  -- create group
  SET new_group_name = CONCAT('content_key_',new_content_key);
  INSERT INTO Groups (group_name,group_createdby_user_key)
  VALUES (new_group_name,valid_content_createdby_user_key);
  SET new_group_key = LAST_INSERT_ID();
  -- create group-user link, set admin
  INSERT INTO Link_Groups_Users (group_key,user_key,is_admin,group_user_createdby_user_key)
  VALUES (new_group_key,valid_content_createdby_user_key,TRUE,valid_content_createdby_user_key);
  -- create group-content link
  UPDATE Content
  SET group_key = new_group_key
  WHERE content_key = new_content_key;
  
  RETURN new_content_key;

END $$


CREATE FUNCTION create_project (
  p_content_title VARCHAR(100),
  p_content_value VARCHAR(1000),
  p_content_createdby_user_key INT
)
RETURNS INT
BEGIN

  DECLARE valid_content_createdby_user_key INT DEFAULT NULL;
  DECLARE new_project_key INT DEFAULT NULL;
  DECLARE new_group_key INT DEFAULT NULL;

  -- validate inputs
  IF p_content_title IS NULL THEN
    RETURN NULL;
  END IF;

  IF p_content_value IS NULL THEN
    RETURN NULL;
  END IF;

  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    RETURN NULL;
  END IF;
  
  -- create new content
  SET new_project_key = create_new_content_key(valid_content_createdby_user_key,NULL);
  -- update with project key
  UPDATE Content
  SET project_key = new_project_key,
      content_title = p_content_title,
      content_value = p_content_value
  WHERE content_key = new_project_key;

  -- return new key
  RETURN new_project_key;

END $$


CREATE PROCEDURE fetch_projects ()
this_procedure:BEGIN

  SELECT project_key,
    content_title,
    content_value,
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key
  FROM `Content`
  WHERE content_key = project_key
    AND content_deleted = FALSE;

END $$


CREATE PROCEDURE fetch_children (
  IN p_parent_content_key INT
)
this_procedure:BEGIN

  SELECT *
  FROM Content
  WHERE
    IF(p_parent_content_key IS NULL,
      parent_content_key IS NULL,
      parent_content_key = p_parent_content_key
    )
    AND content_deleted = FALSE;

END $$


CREATE FUNCTION create_thread (
  p_project_key INT,
  p_content_title VARCHAR(100),
  p_content_value VARCHAR(1000),
  p_content_createdby_user_key INT
)
RETURNS INT
BEGIN

  DECLARE valid_content_createdby_user_key INT DEFAULT NULL;
  DECLARE valid_project_key INT DEFAULT NULL;
  DECLARE new_thread_key INT DEFAULT NULL;
  DECLARE new_comment_key INT DEFAULT NULL;

  -- validate inputs
  IF p_content_title IS NULL THEN
    RETURN NULL;
  END IF;

  IF p_content_value IS NULL THEN
    RETURN NULL;
  END IF;

  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    RETURN NULL;
  END IF;

  SELECT project_key
  INTO valid_project_key
  FROM Content
  WHERE content_key = p_project_key
    AND project_key = p_project_key
    AND content_deleted = FALSE;
  IF valid_project_key IS NULL THEN
    RETURN NULL;
  END IF;

  -- create new thread
  SET new_thread_key = create_new_content_key(valid_content_createdby_user_key,valid_project_key);
  -- update with project key
  UPDATE Content
  SET thread_key = new_thread_key,
      project_key = valid_project_key,
      content_title = p_content_title
  WHERE content_key = new_thread_key;
  
  -- create new comment
  SET new_comment_key = create_comment(p_content_value,valid_project_key,new_thread_key,NULL,valid_content_createdby_user_key);

  -- return new key
  RETURN new_thread_key;

END $$


CREATE FUNCTION create_comment (
  p_content_value VARCHAR(1000),
  p_project_key INT,
  p_thread_key INT,
  p_parent_content_key INT,
  p_content_createdby_user_key INT
)
RETURNS INT
this_procedure:BEGIN

  DECLARE valid_content_createdby_user_key INT DEFAULT NULL;
  DECLARE valid_project_key INT DEFAULT NULL;
  DECLARE valid_thread_key INT DEFAULT NULL;
  DECLARE valid_parent_content_key INT DEFAULT NULL;
  DECLARE new_comment_key INT DEFAULT NULL;
  DECLARE new_comment_title VARCHAR(100);

  -- validate inputs
  IF p_content_value IS NULL THEN
    RETURN NULL;
  END IF;

  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    RETURN NULL;
  END IF;
  
  SELECT project_key
  INTO valid_project_key
  FROM Content
  WHERE content_key = p_project_key
    AND project_key = p_project_key
    AND content_deleted = FALSE;
  IF valid_project_key IS NULL THEN
    RETURN NULL;
  END IF;

  SELECT thread_key, CONCAT('RE: ',content_title)
  INTO valid_thread_key, new_comment_title
  FROM Content
  WHERE content_key = p_thread_key
    AND thread_key = p_thread_key
    AND content_deleted = FALSE;
  IF valid_thread_key IS NULL THEN
    RETURN NULL;
  END IF;
  
  IF p_parent_content_key IS NOT NULL THEN
    SELECT content_key, LEFT(CONCAT('RE: ',content_title),100)
    INTO valid_parent_content_key, new_comment_title
    FROM Content
    WHERE content_key = p_parent_content_key
      AND project_key = p_project_key
      AND thread_key = p_thread_key
      AND content_deleted = FALSE;
    IF valid_project_key IS NULL THEN
      RETURN NULL;
    END IF;
  ELSE
    SET valid_parent_content_key = valid_thread_key;
  END IF;
  
  -- create new content
  SET new_comment_key = create_new_content_key(valid_content_createdby_user_key,valid_parent_content_key);
  -- update with key values, and content value
  UPDATE Content
  SET project_key = valid_project_key,
      thread_key = valid_thread_key,
      content_title = new_comment_title,
      content_value = p_content_value
  WHERE content_key = new_comment_key;

  -- return new key
  RETURN new_comment_key;

END $$


CREATE PROCEDURE edit_content (
  IN p_user_key INT,
  IN p_content_key INT,
  IN p_content_title VARCHAR(100),
  IN p_content_value VARCHAR(1000),
  IN p_content_deleted BOOLEAN
)
this_procedure:BEGIN

  -- validate content exists
  DECLARE valid_content_key INT DEFAULT NULL;
  SELECT content_key INTO valid_content_key
  FROM Content
  WHERE content_key = p_content_key;
  IF valid_content_key IS NULL THEN
    SELECT 'content key does not exist' AS 'ERROR';
    LEAVE this_procedure;
  END IF;

  -- copy current record to history table
  INSERT INTO Content_History (
    content_title,
    content_value,
    project_key,
    thread_key,
    group_key,
    parent_content_key,
    has_children,
    content_key,
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key,
    content_deleted
  ) SELECT
    content_title,
    content_value,
    project_key,
    thread_key,
    group_key,
    parent_content_key,
    has_children,
    content_key,
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key,
    content_deleted
  FROM Content
  WHERE content_key = p_content_key;

  -- update current record
  IF p_content_title IS NOT NULL THEN
    UPDATE Content
    SET content_title = p_content_title
    WHERE content_key = p_content_key;
  END IF;

  IF p_content_value IS NOT NULL THEN
    UPDATE Content
    SET content_value = p_content_value
    WHERE content_key = p_content_key;
  END IF;

  IF p_content_deleted IS NOT NULL THEN
    UPDATE Content
    SET content_deleted = p_content_deleted
    WHERE content_key = p_content_key;
  END IF;

END $$

DELIMITER ;
