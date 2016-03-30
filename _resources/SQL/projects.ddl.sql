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