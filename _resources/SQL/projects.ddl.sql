-- TABLES --
DROP TABLE IF EXISTS Votes;
DROP TABLE IF EXISTS Link_Groups_Content;
DROP TABLE IF EXISTS Link_Groups_Users;
DROP TABLE IF EXISTS Content;
DROP TABLE IF EXISTS Groups;
DROP TABLE IF EXISTS Users;

CREATE TABLE IF NOT EXISTS Users (
  user_key INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(30) NOT NULL UNIQUE,
  username VARCHAR(30) NOT NULL,
  profile_picture VARCHAR(200),
  private_profile BOOLEAN DEFAULT FALSE,
  user_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Groups (
  group_key INT PRIMARY KEY AUTO_INCREMENT,
  group_name VARCHAR(100) NOT NULL,
  group_createdby_user_key INT NOT NULL,
  FOREIGN KEY (group_createdby_user_key) REFERENCES Users(user_key),
  group_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Content (
  content_title VARCHAR(100), -- nullable for comments
  content_value VARCHAR(1000), -- nullable for threads

  content_key INT PRIMARY KEY AUTO_INCREMENT,
  project_key INT,
  FOREIGN KEY (project_key) REFERENCES Content(content_key),
  thread_key INT,
  FOREIGN KEY (thread_key) REFERENCES Content(content_key),

  -- recursive reply-to hierarchy
  parent_content_key INT,
  FOREIGN KEY (parent_content_key) REFERENCES Content(content_key),
  has_children BOOLEAN DEFAULT FALSE,

  -- audit revision history of edits
  original_content_key INT,
  FOREIGN KEY (original_content_key) REFERENCES Content(content_key),
  has_edits BOOLEAN DEFAULT FALSE,

  content_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  content_createdby_user_key INT NOT NULL,
  FOREIGN KEY (content_createdby_user_key) REFERENCES Users(user_key),

  content_edited_time TIMESTAMP NULL,
  content_editedby_user_key INT,
  FOREIGN KEY (content_editedby_user_key) REFERENCES Users(user_key),

  content_deleted_time TIMESTAMP NULL,
  content_deletedby_user_key INT,
  FOREIGN KEY (content_deletedby_user_key) REFERENCES Users(user_key)
);

-- linking tables
CREATE TABLE IF NOT EXISTS Link_Groups_Users (
  group_key INT NOT NULL,
  FOREIGN KEY (group_key) REFERENCES Groups(group_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  is_admin BOOLEAN DEFAULT FALSE,
  group_user_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (group_key,user_key)
);

CREATE TABLE IF NOT EXISTS Link_Groups_Content (
  group_key INT NOT NULL,
  FOREIGN KEY (group_key) REFERENCES Groups(group_key),
  content_key INT NOT NULL,
  FOREIGN KEY (content_key) REFERENCES Content(content_key),
  PRIMARY KEY (group_key,content_key)
);

-- votes
CREATE TABLE IF NOT EXISTS Votes (
  content_key INT NOT NULL,
  FOREIGN KEY (content_key) REFERENCES Content(content_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  vote_value TINYINT NOT NULL, -- downvote = -1, upvote = 1, inappropriate flag = -2
  vote_creation_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  -- in app, don't allow people to send duplicate vote on current, but changes are OK
);

-- STORED PROCEDURES --

DROP PROCEDURE IF EXISTS login_shib_user;
DROP FUNCTION IF EXISTS create_new_content_key;
DROP FUNCTION IF EXISTS create_project;
DROP PROCEDURE IF EXISTS fetch_projects;
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
    RETURN -1;
  END IF;
  
  IF p_parent_content_key IS NOT NULL THEN
    SELECT original_content_key
    INTO valid_parent_content_key
    FROM Content
    WHERE original_content_key = p_parent_content_key
      AND has_edits = FALSE
      AND content_deleted_time IS NULL;
    IF valid_parent_content_key IS NULL THEN
      RETURN -1;
    END IF;
  END IF;
  
  INSERT INTO Content (content_createdby_user_key) VALUES (p_content_createdby_user_key);
  SET new_content_key = LAST_INSERT_ID();
  IF new_content_key IS NOT NULL THEN
    UPDATE Content
    SET original_content_key = new_content_key,
	parent_content_key = valid_parent_content_key
    WHERE content_key = new_content_key;
  END IF;
  
  -- create group
  SET new_group_name = CONCAT('content_key_',new_content_key);
  INSERT INTO Groups (group_name,group_createdby_user_key)
  VALUES (new_group_name,valid_content_createdby_user_key);
  SET new_group_key = LAST_INSERT_ID();
  -- create group-user link, set admin
  INSERT INTO Link_Groups_Users (group_key,user_key,is_admin)
  VALUES (new_group_key,valid_content_createdby_user_key,TRUE);
  -- create group-content link
  INSERT INTO Link_Groups_Content (group_key,content_key)
  VALUES (new_group_key,new_content_key);
  
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
    RETURN -1;
  END IF;

  IF p_content_value IS NULL THEN
    RETURN -1;
  END IF;

  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    RETURN -1;
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

  SELECT content_title,
    content_value,
    original_content_key AS 'content_key',
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key
  FROM `Content`
  WHERE thread_key IS NULL
    AND content_deleted_time IS NULL
    AND has_edits = FALSE;

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
    RETURN -1;
  END IF;

  IF p_content_value IS NULL THEN
    RETURN -1;
  END IF;

  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    RETURN -1;
  END IF;

  SELECT project_key
  INTO valid_project_key
  FROM Content
  WHERE original_content_key = p_project_key
    AND project_key = p_project_key
    AND has_edits = FALSE
    AND content_deleted_time IS NULL;
  IF valid_project_key IS NULL THEN
    RETURN -1;
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

  -- validate inputs
  IF p_content_value IS NULL THEN
    RETURN -1;
  END IF;

  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    RETURN -1;
  END IF;
  
  SELECT project_key
  INTO valid_project_key
  FROM Content
  WHERE original_content_key = p_project_key
    AND project_key = p_project_key
    AND has_edits = FALSE
    AND content_deleted_time IS NULL;
  IF valid_project_key IS NULL THEN
    RETURN -1;
  END IF;

  SELECT thread_key
  INTO valid_thread_key
  FROM Content
  WHERE original_content_key = p_thread_key
    AND thread_key = p_thread_key
    AND has_edits = FALSE
    AND content_deleted_time IS NULL;
  IF valid_thread_key IS NULL THEN
    RETURN -1;
  END IF;
  
  IF p_parent_content_key IS NOT NULL THEN
    SELECT original_content_key
    INTO valid_parent_content_key
    FROM Content
    WHERE original_content_key = p_parent_content_key
      AND project_key = p_project_key
      AND has_edits = FALSE
      AND content_deleted_time IS NULL;
    IF valid_project_key IS NULL THEN
      RETURN -1;
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
      content_value = p_content_value
  WHERE content_key = new_comment_key;

  -- return new key
  RETURN new_comment_key;

END $$


CREATE PROCEDURE edit_content (
  IN p_user_key INT,
  IN p_content_key INT,
  IN p_content_title VARCHAR(100),
  IN p_content_value VARCHAR(1000)
)
this_procedure:BEGIN

  DECLARE valid_content_key INT DEFAULT NULL;
  DECLARE p_project_key INT;
  DECLARE p_thread_key INT;
  DECLARE p_parent_content_key INT;
  DECLARE p_original_content_key INT;
  DECLARE p_has_children BOOLEAN;
  DECLARE p_content_createdby_user_key INT;
  DECLARE p_content_creation_time TIMESTAMP;

  -- get old values
  SELECT content_key,
    project_key,
    thread_key,
    parent_content_key,
    original_content_key,
    has_children,
    content_createdby_user_key,
    content_creation_time
  INTO valid_content_key,
    p_project_key,
    p_thread_key,
    p_parent_content_key,
    p_original_content_key,
    p_has_children,
    p_content_createdby_user_key,
    p_content_creation_time
  FROM Content
  WHERE content_key = p_content_key;
  
  -- validate content exists
  IF valid_content_key IS NULL THEN
    SELECT 'content key does not exist' AS 'ERROR';
    LEAVE this_procedure;
  END IF;
  
  -- mark old content as edited
  UPDATE Content
  SET has_edits = TRUE
  WHERE content_key = valid_content_key;
  
  -- insert new content
  INSERT INTO Content (
    content_title,
    content_value,
    project_key,
    thread_key,
    parent_content_key,
    has_children,
    original_content_key,
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key
  )
  VALUES (
    p_content_title,
    p_content_value,
    p_project_key,
    p_thread_key,
    p_parent_content_key,
    p_has_children,
    p_original_content_key,
    p_content_creation_time,
    p_content_createdby_user_key,
    CURRENT_TIMESTAMP,
    p_user_key
  );

END $$

DELIMITER ;
