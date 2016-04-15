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

DROP PROCEDURE IF EXISTS test_proc;
DROP PROCEDURE IF EXISTS create_reply;
DROP FUNCTION IF EXISTS authorize_content_editor;
DROP PROCEDURE IF EXISTS import_new_deltaprime_project;
DROP PROCEDURE IF EXISTS import_new_deltaprime_comment;

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

CREATE FUNCTION authorize_content_editor (
  p_user_key INT,
  p_content_key INT
)
RETURNS VARCHAR(20)
BEGIN

  DECLARE valid_user_key INT DEFAULT NULL;
  DECLARE valid_content_key INT DEFAULT NULL;
  DECLARE valid_thread_key INT DEFAULT NULL;
  DECLARE valid_project_key INT DEFAULT NULL;
  DECLARE authorized_user_key INT DEFAULT NULL;

  -- parameter validation
  SELECT user_key
  INTO valid_user_key
  FROM Users
  WHERE user_key = p_user_key;
  IF valid_user_key IS NULL THEN
    RETURN 'invalid p_user_key';
  END IF;

  SELECT content_key, thread_key, project_key
  INTO valid_content_key, valid_thread_key, valid_project_key
  FROM Content
  WHERE content_key = p_content_key;
  IF valid_content_key IS NULL THEN
    RETURN 'invalid p_content_key';
  END IF;

  -- for performance considerations, authority is restricted to three tiers
  -- content, thread, project
  SELECT user_key
  INTO authorized_user_key
  FROM Content_Editors
  WHERE user_key = valid_user_key
    AND (
      content_key = valid_content_key OR
      content_key = valid_thread_key OR
      content_key = valid_project_key
    )
  LIMIT 1;

  IF authorized_user_key IS NOT NULL THEN
    RETURN 'authorized';
  END IF;
  
  SELECT content_createdby_user_key
  INTO authorized_user_key
  FROM Content
  WHERE content_createdby_user_key = valid_user_key
    AND content_key = valid_content_key;
  
  IF authorized_user_key IS NOT NULL THEN
    RETURN 'authorized';
  ELSE
    RETURN 'UNAUTHORIZED';
  END IF;

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
  
  SELECT new_content_key AS 'new_content_key';

END $$


CREATE PROCEDURE get_content (
  IN p_content_key INT,
  IN children BOOLEAN,
  IN p_user_key INT
)
this_procedure:BEGIN

  DECLARE authorization_msg VARCHAR(20) DEFAULT NULL;
  DECLARE authorized_editor BOOLEAN DEFAULT 0;

  IF p_user_key IS NOT NULL AND p_content_key IS NOT NULL THEN
    SET authorization_msg = authorize_content_editor(p_user_key,p_content_key);
    IF authorization_msg = 'authorized' THEN
      SET authorized_editor = 1;
    END IF;
    -- get user vote
--     SELECT vote_value INTO my_vote
--     FROM Votes
--     WHERE content_key = p_content_key
--       AND user_key = p_user_key;
  END IF;

  SELECT c.*,
    uc.username AS 'content_createdby_username',
    ue.username AS 'content_editedby_username',
    authorized_editor
    ,SUM(v.vote_value) AS 'total_votes'
    ,mv.vote_value AS 'my_vote'
  FROM Content c
  LEFT JOIN Users uc
    ON c.content_createdby_user_key = uc.user_key
  LEFT JOIN Users ue
    ON c.content_editedby_user_key = ue.user_key
  LEFT JOIN Votes v
    ON c.content_key = v.content_key
  LEFT JOIN Votes mv
    ON c.content_key = mv.content_key AND mv.user_key = p_user_key
  WHERE
    IF(children = TRUE,
      IF(p_content_key IS NULL,
        parent_content_key IS NULL,
        parent_content_key = p_content_key
      )
      AND c.content_key > 0,
    -- else
      c.content_key = p_content_key
    )
    AND content_deleted = FALSE
  GROUP BY c.content_key;

END $$

CREATE PROCEDURE read_content (
  IN p_content_key INT,
  IN p_user_key INT
)
this_procedure:BEGIN

  CALL get_content(p_content_key,FALSE,p_user_key);

END $$


CREATE PROCEDURE fetch_children (
  IN p_parent_content_key INT,
  IN p_user_key INT
)
this_procedure:BEGIN

  CALL get_content(p_parent_content_key,TRUE,p_user_key);

END $$


CREATE PROCEDURE fetch_projects ()
this_procedure:BEGIN

  SELECT 
    SUM(v.vote_value) AS 'total_votes',
    c.content_title,
    c.content_key,
    c.content_creation_time,
    uc.username AS 'content_createdby_username',
    MAX(c.content_edited_time) AS 'last_updated',
    (COUNT(c.content_key) - 1) AS 'total_comments'
  FROM Content c
  LEFT JOIN Users uc
    ON c.content_createdby_user_key = uc.user_key
  LEFT JOIN Votes v
    ON c.content_key = v.content_key
  WHERE  c.content_key > 0
    AND parent_content_key IS NULL
    AND content_deleted = FALSE
  GROUP BY c.project_key;

END $$


CREATE PROCEDURE update_content (
  IN p_user_key INT,
  IN p_content_key INT,
  IN p_content_title VARCHAR(100),
  IN p_content_value VARCHAR(1000),
  IN p_content_deleted BOOLEAN
)
this_procedure:BEGIN

  DECLARE authorization_msg VARCHAR(20) DEFAULT NULL;
  DECLARE valid_content_key INT DEFAULT NULL;
  DECLARE valid_content_editedby_user_key INT DEFAULT NULL;
  DECLARE deleted_pkey INT DEFAULT NULL;
  DECLARE count_children INT DEFAULT NULL;

  -- validate
  SELECT content_key, parent_content_key
  INTO valid_content_key, deleted_pkey
  FROM Content
  WHERE content_key = p_content_key;
  IF valid_content_key IS NULL THEN
    SELECT 'content key does not exist' AS 'ERROR';
    LEAVE this_procedure;
  END IF;

  SELECT user_key
  INTO valid_content_editedby_user_key
  FROM Users
  WHERE user_key = p_user_key;
  IF valid_content_editedby_user_key IS NULL THEN
    SELECT 'invalid p_content_createdby_user_key' AS 'ERROR';
    LEAVE this_procedure;
  END IF;

  -- authorize
  SET authorization_msg = authorize_content_editor(valid_content_editedby_user_key,valid_content_key);
  IF authorization_msg <> 'authorized' THEN
    SELECT authorization_msg AS 'ERROR';
    LEAVE this_procedure;
  END IF;

  -- copy current record to history table
  INSERT INTO Content_History (
    content_title,
    content_value,
    project_key,
    thread_key,
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
    parent_content_key,
    has_children,
    content_key,
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key,
    content_deleted
  FROM Content
  WHERE content_key = valid_content_key;

  -- update current record
  IF p_content_title IS NOT NULL THEN
    UPDATE Content
    SET content_title = p_content_title
    WHERE content_key = valid_content_key;
  END IF;

  IF p_content_value IS NOT NULL THEN
    UPDATE Content
    SET content_value = p_content_value
    WHERE content_key = valid_content_key;
  END IF;

  IF p_content_deleted IS NOT NULL THEN
    UPDATE Content
    SET content_deleted = p_content_deleted
    WHERE content_key = valid_content_key;

    SELECT COUNT(content_key) INTO count_children
    FROM `Content`
    WHERE parent_content_key = deleted_pkey
      AND content_deleted = FALSE;
    IF count_children < 1 THEN
      UPDATE Content SET has_children = FALSE
      WHERE content_key = deleted_pkey ;
    END IF;
  END IF;
  
  UPDATE Content
  SET content_edited_time = CURRENT_TIMESTAMP,
      content_editedby_user_key = p_user_key
  WHERE content_key = valid_content_key;
  
  IF p_content_deleted = TRUE THEN
    SELECT 'deleted' AS 'success';
  ELSE
    SELECT 'updated' AS 'success';
  END IF;
  
END $$


CREATE PROCEDURE import_new_deltaprime_project (
  p_content_creation_time TIMESTAMP,
  p_content_edited_time TIMESTAMP,
  p_content_title VARCHAR(100),
  p_content_value VARCHAR(1000),
  p_Project_Id INT
)
this_procedure:BEGIN

  DECLARE new_content_key INT DEFAULT NULL;

  INSERT INTO Content (
    content_title,
    content_value,
    project_key,
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key
  ) VALUES (
    p_content_title,
    p_content_value,
    NULL,
    p_content_creation_time,-2,
    p_content_edited_time,-2
  );
  
  SET new_content_key = LAST_INSERT_ID();
  UPDATE Content SET project_key = new_content_key WHERE content_key = new_content_key;
  
  INSERT INTO Link_DeltaPrimeProjects VALUES (p_Project_Id,new_content_key);

END $$


CREATE PROCEDURE import_new_deltaprime_comment (
  p_content_title VARCHAR(100),
  p_content_value VARCHAR(1000),
  p_project_key INT,
  p_content_creation_time TIMESTAMP,
  p_content_edited_time TIMESTAMP,
  p_Comment_Id INT
)
this_procedure:BEGIN

  DECLARE new_content_key INT DEFAULT NULL;

  INSERT INTO Content (
    content_title,
    content_value,
    project_key,
    parent_content_key,
    content_creation_time,
    content_createdby_user_key,
    content_edited_time,
    content_editedby_user_key
  ) VALUES (
    p_content_title,
    p_content_value,
    p_project_key,
    p_project_key,
    p_content_creation_time,-2,
    p_content_edited_time,-2
  );
  
  SET new_content_key = LAST_INSERT_ID();
  UPDATE Content SET thread_key = new_content_key WHERE content_key = new_content_key;
  UPDATE Content SET has_children = TRUE WHERE content_key = p_project_key;
  
  INSERT INTO Link_DeltaPrimeComments VALUES (p_Comment_Id,new_content_key);

END $$

DELIMITER ;
