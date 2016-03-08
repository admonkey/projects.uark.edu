-- create thread
-- INSERT INTO Content (content_title, project_key, parent_content_key, content_createdby_user_key)
--   VALUES
-- ('first thread on project', 1, 1, 1),
-- ('second thread on project', 1, 1, 2);
-- -- create comment
-- INSERT INTO Content (content_value, project_key, thread_key, parent_content_key, content_createdby_user_key)
--   VALUES
-- ('this is the 1st message in the 1st thread of the 1st project.', 1, 2, 2, 1),
-- ('this is the 2nd message in the 1st thread of the 1st project.', 1, 2, 2, 2),
-- ('this is the 1st reply to the 1st message in the 1st thread of the 1st project.', 1, 2, 4, 2);

-- SELECT create_project('Project Hello World!', 'This is the 1st project.', 1);
-- SELECT create_project('Project 2', 'This is the 2nd project.', 2);
-- SELECT create_thread(1,'1st thread on project 1','this is the 1st message in the 1st thread of the 1st project.',1);
-- SELECT create_comment('this is the 2nd message in the 1st thread of the 1st project.',1,3,NULL,2);
-- SELECT create_comment('this is the 1st reply to the 1st message in the 1st thread of the 1st project.',1,3,4,2);
-- SELECT create_thread(1,'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','this is the 2nd thread of the 1st project.',1);
-- SELECT create_comment('this is the 1st message in the 2nd thread of the 1st project.',1,7,8,2);
-- SELECT create_comment('this is the 2nd message in the 2nd thread of the 1st project.',1,7,9,2);

CALL create_content(1,NULL,'Project Hello World!', 'This is the 1st project.');
CALL create_content(1,NULL,'Project 2', 'This is the 2nd project.');
CALL create_content(2,1,'1st thread on project 1','this is the 1st message in the 1st thread of the 1st project.');
CALL create_content(1,3,NULL, 'this is the 2nd message in the 1st thread of the 1st project.');
CALL create_content(2,4,NULL, 'this is the 1st reply to the 2nd message in the 1st thread of the 1st project.');
CALL create_content(3,3,NULL, 'this is the 3rd message in the 1st thread of the 1st project.');
CALL create_content(4,1,'2nd thread on project 1','this is the 1st message in the 2nd thread of the 1st project.');
CALL create_content(4,2,'1st thread on project 2','this is the 1st message in the 1st thread of the 2nd project.');
CALL create_content(1,8,NULL, 'this is the 2nd message in the 1st thread of the 2nd project.');
