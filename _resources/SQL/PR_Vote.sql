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
  
   
    select user_key into user_key_check from Users where user_key = p_user_key;
        select user_key_check;
        
    select content_key into content_key_check from Content where content_key = p_content_key;
    	select content_key_check;
    	
  -- There will be only one user and content
  
    IF user_key_check IS NULL THEN
        select 'invalid user' as 'ERROR';
        LEAVE this_procedure;  
    END IF;
    
	IF content_key_check IS NULL  THEN
        select 'invalid content' as 'ERROR';
        LEAVE this_procedure;
    END IF;
    
     select content_deleted 
        into content_status 
        from Content
        where content_key = p_content_key;
     
     SELECT content_status; 
     
     
     select vote_value 
            into existing_vote_value 
            from Votes 
            where content_key = p_content_key
             and user_key = p_user_key;
             
         SELECT existing_vote_value;
            
      
      	IF existing_vote_value = p_vote_value THEN
            -- Already voted with same value
            LEAVE this_procedure;
    	END IF;
     
    IF (p_vote_value = 1 or p_vote_value =-1 or p_vote_value =-2 )   THEN
    	
        SELECT 'INSERT NEW RECORD' as 'message';
	
    	
        SELECT vote_creation_time 
    	into existing_vote_time
    	from Votes 
    	where content_key = p_content_key 
        and user_key = p_user_key;
        
        if (existing_vote_time is NOT null) THEN
       
        INSERT into Votes_History (content_key, user_key, vote_value,  vote_creation_time)
        values (p_content_key,p_user_key,existing_vote_value, existing_vote_time);
        
        END IF;
        
        
        
        -- DELETE
        DELETE from Votes
        where content_key = p_content_key 
        and user_key = p_user_key;
        
        select 'b4 NEW RECORD' as 'message';
        
        INSERT into Votes (content_key, user_key, vote_value) values (p_content_key,p_user_key,p_vote_value);
            
        select 'AFTER NEW RECORD' as 'message';
    
    ELSE
        select 'invalid vote value' as 'ERROR';
        LEAVE this_procedure;
    END IF;
       
END $$

DELIMITER ; 