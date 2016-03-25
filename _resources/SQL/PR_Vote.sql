DROP PROCEDURE IF EXISTS create_vote;

DELIMITER $$
CREATE PROCEDURE create_vote (
  IN p_user_key INT,
  IN p_content_key INT,
  IN p_vote_value INT
)

this_procedure:BEGIN

  DECLARE existing_vote_value INT DEFAULT NULL;
  DECLARE valid_number INT DEFAULT NULL;
  DECLARE existing_vote_time TIMESTAMP DEFAULT NULL;
  DECLARE user_key_check    INT DEFAULT NULL;
  DECLARE content_key_check INT DEFAULT NULL;
  DECLARE content_status    INT DEFAULT NULL;

  IF (p_vote_value <> 1 AND p_vote_value <> -1 AND p_vote_value <> -2) THEN
    SELECT 'invalid vote value' AS 'ERROR';
    LEAVE this_procedure;
  END IF;

  SELECT user_key INTO user_key_check
  FROM Users WHERE user_key = p_user_key;
  IF user_key_check IS NULL THEN
      SELECT 'invalid user' AS 'ERROR';
      LEAVE this_procedure;  
  END IF;
      
  SELECT content_key INTO content_key_check
  FROM Content WHERE content_key = p_content_key;
  IF content_key_check IS NULL  THEN
      SELECT 'invalid content' AS 'ERROR';
      LEAVE this_procedure;
  END IF;
     
  SELECT vote_value, vote_creation_time
  INTO existing_vote_value, existing_vote_time
  FROM Votes 
  WHERE content_key = p_content_key
    AND user_key = p_user_key;
             
  IF existing_vote_value = p_vote_value THEN
      -- Already voted with same value
      SELECT 'already voted this way' AS 'message';
      LEAVE this_procedure;
  END IF;

  -- create audit history
  IF (existing_vote_time IS NOT NULL) THEN
    INSERT INTO Votes_History (content_key, user_key, vote_value,  vote_creation_time)
    VALUES (p_content_key,p_user_key,existing_vote_value, existing_vote_time);
  END IF;
        
  -- DELETE
  DELETE FROM Votes
  WHERE content_key = p_content_key 
  AND user_key = p_user_key;
        
  INSERT INTO Votes (content_key, user_key, vote_value) VALUES (p_content_key,p_user_key,p_vote_value);
  
  SELECT 'created new vote' AS 'message';
    
END $$

DELIMITER ; 