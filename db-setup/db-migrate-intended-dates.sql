use usermanagement;

DELIMITER $$
CREATE PROCEDURE UpdateIntendedDates()
BEGIN
	DECLARE finished INTEGER DEFAULT 0;
	DECLARE clockin DATETIME DEFAULT NULL;
	DECLARE intended_date DATETIME DEFAULT NULL;

	-- declare cursor for employee email
	DEClARE curDate 
		CURSOR FOR 
			SELECT clockin, intended_date FROM attendances;

	-- declare NOT FOUND handler
	DECLARE CONTINUE HANDLER 
        FOR NOT FOUND SET finished = 1;

	OPEN curDate;

	getEmail: LOOP
		FETCH curDate INTO emailAddress;
		IF finished = 1 THEN 
			LEAVE getEmail;
		END IF;
		-- build email list
		SET emailList = CONCAT(emailAddress,";",emailList);
	END LOOP getEmail;
	CLOSE curDate;

END$$
DELIMITER ;