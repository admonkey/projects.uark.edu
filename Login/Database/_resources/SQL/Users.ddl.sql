CREATE TABLE IF NOT EXISTS `Users` (
  `user_key` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_creation_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` VARCHAR(30) NOT NULL UNIQUE,
  `email` VARCHAR(50) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `private_profile` BIT(1) DEFAULT 0,
  `password` CHAR(128) NOT NULL,
  `salt` CHAR(128) NOT NULL
);
-- ROOT USER PASSWORD IS P@55W0rd
INSERT INTO `Users` (username,email,first_name,last_name,private_profile,password,salt)
  VALUES (
    'root','root@example.com','root','user',1,
    'e491685a7e7ea32116eadd3911848b22b734fd3685796c719c1b14fca0c76a5efede54b7e48f569d8579e1a295145e8aaf8053e735e2c692dc80528fe02670be',
    'ec51c8c90854b1d9f18a0ac9daa75d611c3d01f7ac4a12059439d730322659d3528596b6c674d8ea3ae8d14b8b552cb0b46c32912ae82e470bc52d04bd229b88'
  );


CREATE TABLE IF NOT EXISTS `User_Login_Attempts` (
  `user_key` INT NOT NULL,
  `time` VARCHAR(30) NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key)
);


CREATE TABLE IF NOT EXISTS `User_Groups` (
  `group_key` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `group_creation_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_name` VARCHAR(30) NOT NULL UNIQUE,
  `group_createdby_user_key` INT NOT NULL,
  FOREIGN KEY (group_createdby_user_key) REFERENCES Users(user_key)
);
INSERT INTO `User_Groups` (group_name,group_createdby_user_key) VALUES ('ADMIN',1);


CREATE TABLE IF NOT EXISTS `User_Groups-link` (
  `user_key` INT NOT NULL,
  FOREIGN KEY (user_key) REFERENCES Users(user_key),
  `group_key` INT NOT NULL,
  FOREIGN KEY (group_key) REFERENCES User_Groups(group_key),
  `membership_creation_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(user_key,group_key)
);
INSERT INTO `User_Groups-link` (user_key,group_key) VALUES (1,1);
