DELIMITER $$
CREATE PROCEDURE create_vote_PROD (
  IN p_user_key INT,
  IN p_content_key INT,
  IN p_vote_value INT
)

this_procedure:BEGIN

  DECLARE existing_vote_value INT DEFAULT NULL;
  DECLARE valid_number INT DEFAULT NULL;
  DECLARE existing_vote_time TIMESTAMP DEFAULT NULL;
  DECLARE valid_user_count_1 INT DEFAULT NULL;
  DECLARE valid_user_count_2 INT DEFAULT NULL;

  
  
  -- validate user_key
  -- validate content_key
  -- validate vote_value value (either -2, -1, or 1)
  -- count the user_key and content_key (should be 1) and exit the stored procedure if not.
  
  
    select count(*) into valid_user_count_1 from Users where user_key = p_user_key;
    
    select count(*) into valid_user_count_2 from Content where content_key = p_content_key;
    
  -- There will be only one user and content
    IF valid_user_count_1 != 1 THEN
        select 'invalid user' as 'ERROR';
        LEAVE this_procedure;  
    END IF;
    
    IF valid_user_count_2 != 1  THEN
        select 'invalid content' as 'ERROR';
        LEAVE this_procedure;
    END IF;
    
    IF (p_vote_value = 1 or p_vote_value =-1 or p_vote_value =-2 or p_vote_value = NULL)   THEN
    	
    	-- select 'valid vote value' as 'MESSAGE';
        -- STORING THE EXISTING VOTE VALUE
        
        select vote_value 
            into existing_vote_value 
            from Votes 
            where content_key = p_content_key
             and user_key = p_user_key;
             
        -- To check same vote value or not
        
        IF existing_vote_value = p_vote_value THEN
            -- Already voted with same value
            LEAVE this_procedure;
        END IF;
    ELSE
        select 'invalid vote value' as 'ERROR';
        LEAVE this_procedure;
    END IF;


--  INSERT NEW RECORD
	IF existing_vote_value IS NULL THEN
    
    	INSERT into Votes (content_key, user_key, vote_value) values 	 (p_content_key,p_user_key,p_vote_value);
	ELSE
    	-- 'vote already there' 
        SELECT vote_creation_time 
    	into existing_vote_time
    	from Votes 
    	where content_key = p_content_key 
        and user_key = p_user_key;
        
        INSERT into Votes_History (content_key, user_key, vote_value,  vote_creation_time)
        values (p_content_key,p_user_key,existing_vote_value, existing_vote_time);
        
        -- DELETE
    DELETE from Votes
    where content_key = p_content_key 
        and user_key = p_user_key;
        
        -- INSERT NEW RECORD
    INSERT into Votes (content_key, user_key, vote_value) values (p_content_key,p_user_key,p_vote_value);
    END IF;

END $$

DELIMITER ;  