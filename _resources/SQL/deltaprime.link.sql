DROP TABLE IF EXISTS Link_DeltaPrimeComments;
DROP TABLE IF EXISTS Link_DeltaPrimeProjects;

CREATE TABLE IF NOT EXISTS Link_DeltaPrimeProjects (
  Project_Id INT PRIMARY KEY NOT NULL,
  project_key INT NOT NULL UNIQUE,
  FOREIGN KEY (project_key) REFERENCES Content(content_key)
);

CREATE TABLE IF NOT EXISTS Link_DeltaPrimeComments (
  Comment_Id INT PRIMARY KEY NOT NULL,
  content_key INT NOT NULL UNIQUE,
  FOREIGN KEY (content_key) REFERENCES Content(content_key)
);

DELETE FROM Users WHERE user_key = -2;
INSERT INTO Users (email,username, user_key) VALUES ('deltaprime','deltaprime', -2);

DROP FUNCTION IF EXISTS import_new_deltaprime_project;
DELIMITER $$
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
  
  INSERT INTO Link_DeltaPrimeProjects VALUES (p_Project_Id,new_content_key);

END $$
DELIMITER ;