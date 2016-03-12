-- TABLES --
DROP TABLE IF EXISTS Link_Groups_Content;
DROP TABLE IF EXISTS Link_Groups_Users_History;
DROP TABLE IF EXISTS Link_Groups_Users;
DROP TABLE IF EXISTS Votes_History;
DROP TABLE IF EXISTS Votes;
DROP TABLE IF EXISTS Content_Editors_History;
DROP TABLE IF EXISTS Content_Editors;
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


CREATE TABLE IF NOT EXISTS Content (
  content_title VARCHAR(100) NOT NULL,
  content_value VARCHAR(1000) NOT NULL,

  project_key INT,
  FOREIGN KEY (project_key) REFERENCES Content(content_key),
  thread_key INT,
  FOREIGN KEY (thread_key) REFERENCES Content(content_key),

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
  content_title VARCHAR(100) NOT NULL,
  content_value VARCHAR(1000) NOT NULL,
  
  project_key INT,
  FOREIGN KEY (project_key) REFERENCES Content(content_key),
  thread_key INT,
  FOREIGN KEY (thread_key) REFERENCES Content(content_key),

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


CREATE TABLE IF NOT EXISTS Content_Editors (
  content_key INT NOT NULL,
  FOREIGN KEY (content_key) REFERENCES Content(content_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  is_admin BOOLEAN NOT NULL DEFAULT FALSE,
  
  PRIMARY KEY (content_key,user_key),
  content_editor_creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  content_editor_createdby_user_key INT NOT NULL,
  FOREIGN KEY (content_editor_createdby_user_key) REFERENCES Users(user_key),

  content_editor_edited_time TIMESTAMP NULL,
  content_editor_editedby_user_key INT,
  FOREIGN KEY (content_editor_editedby_user_key) REFERENCES Users(user_key),
  content_editor_deleted BOOLEAN DEFAULT FALSE
);
CREATE TABLE IF NOT EXISTS Content_Editors_History (
  content_key INT NOT NULL,
  FOREIGN KEY (content_key) REFERENCES Content(content_key),
  user_key INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  is_admin BOOLEAN NOT NULL,
  
  content_editor_creation_time TIMESTAMP,
  content_editor_createdby_user_key INT NOT NULL,
  FOREIGN KEY (content_editor_createdby_user_key) REFERENCES Users(user_key),

  content_editor_edited_time TIMESTAMP NULL,
  content_editor_editedby_user_key INT,
  FOREIGN KEY (content_editor_editedby_user_key) REFERENCES Users(user_key),
  content_editor_deleted BOOLEAN NOT NULL
);


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
-- old
DROP FUNCTION IF EXISTS create_new_content_key;
DROP PROCEDURE IF EXISTS edit_content;
DROP FUNCTION IF EXISTS create_project;
DROP PROCEDURE IF EXISTS fetch_projects;
DROP PROCEDURE IF EXISTS fetch_project;
DROP PROCEDURE IF EXISTS fetch_threads;
DROP PROCEDURE IF EXISTS fetch_thread;
DROP FUNCTION IF EXISTS create_thread;
DROP FUNCTION IF EXISTS create_comment;
DROP PROCEDURE IF EXISTS fetch_comment;
-- current
DROP PROCEDURE IF EXISTS login_shib_user;
DROP PROCEDURE IF EXISTS create_content;
DROP PROCEDURE IF EXISTS update_content;
DROP PROCEDURE IF EXISTS fetch_children;
DROP PROCEDURE IF EXISTS read_content;
DROP PROCEDURE IF EXISTS get_content;
DROP PROCEDURE IF EXISTS create_vote;
DROP PROCEDURE IF EXISTS test_proc;
DROP PROCEDURE IF EXISTS create_reply;
-- TODO:
DROP PROCEDURE IF EXISTS delete_content;

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

CREATE PROCEDURE create_reply(
  p_content_createdby_user_key INT,
  p_parent_content_key INT,
  p_content_value VARCHAR(1000)
)
this_procedure:BEGIN

  CALL create_content(p_content_createdby_user_key,p_parent_content_key,NULL,p_content_value);

END $$

CREATE PROCEDURE create_content (
  p_content_createdby_user_key INT,
  p_parent_content_key INT,
  p_content_title VARCHAR(100),
  p_content_value VARCHAR(1000)
)
this_procedure:BEGIN

  DECLARE valid_content_createdby_user_key INT DEFAULT NULL;
  DECLARE valid_parent_content_key INT DEFAULT NULL;
  DECLARE new_content_title VARCHAR(100) DEFAULT NULL;
  DECLARE new_content_key INT DEFAULT NULL;
  DECLARE parent_project_key INT DEFAULT NULL;
  DECLARE parent_thread_key INT DEFAULT NULL;

  -- parameter validation
  IF
    p_parent_content_key IS NULL
    AND p_content_title IS NULL
  THEN
    SELECT 'p_parent_content_key & p_content_title cannot be null' AS 'ERROR';
    LEAVE this_procedure;
  END IF;

  SELECT user_key
  INTO valid_content_createdby_user_key
  FROM Users
  WHERE user_key = p_content_createdby_user_key;
  IF valid_content_createdby_user_key IS NULL THEN
    SELECT 'invalid p_content_createdby_user_key' AS 'ERROR';
    LEAVE this_procedure;
  END IF;

  IF p_parent_content_key IS NOT NULL THEN
    SELECT content_key, LEFT(CONCAT('RE: ',content_title),100),
      project_key, thread_key
    INTO valid_parent_content_key, new_content_title,
      parent_project_key, parent_thread_key
    FROM Content
    WHERE content_key = p_parent_content_key
      AND content_deleted = FALSE;
    IF valid_parent_content_key IS NULL THEN
      SELECT 'invalid p_parent_content_key' AS 'ERROR';
      LEAVE this_procedure;
    ELSE
      UPDATE Content
      SET has_children = TRUE
      WHERE content_key = valid_parent_content_key;
    END IF;
  END IF;

  IF p_content_title IS NOT NULL THEN
    SET new_content_title = p_content_title;
  END IF;

  -- create record
  INSERT INTO Content (
    content_createdby_user_key,
    parent_content_key,
    content_title,
    content_value,
    project_key,
    thread_key
  )
  VALUES (
    valid_content_createdby_user_key,
    valid_parent_content_key,
    new_content_title,
    p_content_value,
    parent_project_key,
    parent_thread_key
  );
  SET new_content_key = LAST_INSERT_ID();

  -- pseudo indexes
  IF parent_thread_key IS NULL THEN
    IF parent_project_key IS NULL THEN
      SET parent_project_key = new_content_key;
    ELSE
      SET parent_thread_key = new_content_key;
    END IF;
    UPDATE Content
    SET project_key = parent_project_key,
	thread_key = parent_thread_key
    WHERE content_key = new_content_key;
  END IF;

  -- create security group
  INSERT INTO Content_Editors (
    content_key,
    user_key,
    is_admin,
    content_editor_createdby_user_key
  ) VALUES (
    new_content_key,
    valid_content_createdby_user_key,
    TRUE,
    valid_content_createdby_user_key
  );

END $$


CREATE PROCEDURE get_content (
  IN p_content_key INT,
  IN children BOOLEAN
)
this_procedure:BEGIN

  SELECT c.*,
    uc.username AS 'content_createdby_username',
    ue.username AS 'content_editedby_username'
  FROM Content c
  LEFT JOIN Users uc
    ON c.content_createdby_user_key = uc.user_key
  LEFT JOIN Users ue
    ON c.content_editedby_user_key = ue.user_key
  WHERE
    
    IF(children = TRUE,
      IF(p_content_key IS NULL,
	parent_content_key IS NULL,
	parent_content_key = p_content_key
      )
      AND content_key > 0,
    -- else
      content_key = p_content_key
    )
    AND content_deleted = FALSE;

--   -- read content
--   WHERE
--     content_key = p_content_key
--     AND content_deleted = FALSE;
--   
--   -- fetch_children
--   WHERE
--     IF(p_parent_content_key IS NULL,
--       parent_content_key IS NULL,
--       parent_content_key = p_parent_content_key
--     )
--     AND content_key > 0
--     AND content_deleted = FALSE;

END $$

CREATE PROCEDURE read_content (
  IN p_content_key INT
)
this_procedure:BEGIN

  CALL get_content(p_content_key,FALSE);

END $$


CREATE PROCEDURE fetch_children (
  IN p_parent_content_key INT
)
this_procedure:BEGIN

  CALL get_content(p_parent_content_key,TRUE);

END $$


CREATE PROCEDURE update_content (
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
