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
  
  -- validate user_key
  -- validate content_key
  -- validate vote_value value (either -2, -1, or 1)
  -- count the user_key and content_key (should be 1) and exit the stored procedure if not.
  
  
    select user_key into user_key_check from Users where user_key = p_user_key;
        select user_key_check;
    select content_key into content_key_check from Content where content_key = p_content_key;
    
  -- There will be only one user and content
    IF user_key_check != p_user_key THEN
        select 'invalid user' as 'ERROR';
        LEAVE this_procedure;  
    END IF;
    
    IF content_key_check IS NULL  THEN
        select 'invalid content' as 'ERROR';
        LEAVE this_procedure;
    END IF;
    
    select content_deleted 
        into content_status 
        from content
        where content_key = p_content_key;
    
    -- Deleted cannot be voted
    
    IF (content_status = 1) THEN
        LEAVE this_procedure;
    END IF;
    
    select vote_value 
            into existing_vote_value 
            from Votes 
            where content_key = p_content_key
             and user_key = p_user_key;
                
    IF existing_vote_value = p_vote_value THEN
            -- Already voted with same value
            LEAVE this_procedure;
    END IF;
    
    IF (p_vote_value = 1 or p_vote_value =-1 or p_vote_value =-2 )   THEN
    	
    	-- finding the existing vote time
        SELECT vote_creation_time 
    	into existing_vote_time
    	from Votes 
    	where content_key = p_content_key 
        and user_key = p_user_key;
        -- inserting into votes history
        INSERT into Votes_History (content_key, user_key, vote_value,  vote_creation_time)
        values (p_content_key,p_user_key,existing_vote_value, existing_vote_time);
            -- DELETE
        DELETE from Votes
        where content_key = p_content_key 
        and user_key = p_user_key;

         -- INSERT NEW RECORD
            INSERT into Votes (content_key, user_key, vote_value) values (p_content_key,p_user_key,p_vote_value);
    
    
    ELSE
        select 'invalid vote value' as 'ERROR';
        LEAVE this_procedure;
        END IF;
       

END $$

DELIMITER ;  