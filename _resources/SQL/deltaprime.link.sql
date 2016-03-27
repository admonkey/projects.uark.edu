DROP TABLE IF EXISTS Link_DeltaPrimeComments;
DROP TABLE IF EXISTS Link_DeltaPrimeProjects;

CREATE TABLE IF NOT EXISTS Link_DeltaPrimeProjects (
  Project_Id INT PRIMARY KEY NOT NULL,
  project_key INT NOT NULL,
  FOREIGN KEY (project_key) REFERENCES Content(content_key)
);

CREATE TABLE IF NOT EXISTS Link_DeltaPrimeComments (
  Comment_Id INT PRIMARY KEY NOT NULL,
  content_key INT NOT NULL,
  FOREIGN KEY (content_key) REFERENCES Content(content_key)
);

DELETE FROM Users WHERE user_key = -2;
INSERT INTO Users (email,username, user_key) VALUES ('deltaprime','deltaprime', -2);
