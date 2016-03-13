-- SYSTEM ADMINISTRATION
INSERT INTO Users (email,username, user_key) VALUES ('root','root', -1);
INSERT INTO Content (content_key,content_createdby_user_key,parent_content_key,content_title,content_value)
  VALUES (-1,-1,NULL,'ALL CONTENT','ALL CONTENT');
INSERT INTO Content_Editors (content_key,user_key,is_admin,content_editor_createdby_user_key)
  VALUES (-1,-1,TRUE,-1);
