/*
	job_type - <select> with job type in HTML
	status - for the manager, states the request's status (new, handled, closed etc.. as Numbers)
*/
CREATE TABLE IF NOT EXISTS Clients(
	client_id INT NOT NULL UNIQUE,
	job_type CHAR(60) NOT NULL,
	firstname CHAR(40) NOT NULL,
	lastname CHAR(40) NOT NULL,
	city CHAR(40) NOT NULL,
	address CHAR(60) NOT NULL,
	email CHAR(100) NOT NULL,
	phone_number CHAR(10) NOT NULL,
	description CHAR(255) NOT NULL,
	status TINYINT(1) UNSIGNED NOT NULL,
	request_date DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (client_id)
)CHARACTER SET utf8;

/*
	Images uploaded to the server are stored in folders, folder paths are kept here
*/
CREATE TABLE IF NOT EXISTS Images(
	client_id INT NOT NULL,
	images_path char(255) NOT NULL,
	FOREIGN KEY (client_id) 
		REFERENCES Clients(client_id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)CHARACTER SET utf8;


/*
	Manager's table (request handlers)
	
	DO NOT REDUCE the password's length, 255 is for future hash algorithms
*/
CREATE TABLE IF NOT EXISTS Manage(
	username CHAR(72) NOT NULL,
	password CHAR(255) NOT NULL,
	PRIMARY KEY (username)
)CHARACTER SET utf8;


/*
	Jobs available for the clients to choose in the client form
	jobs can be later added/removed from this table
*/
CREATE TABLE IF NOT EXISTS Jobs(
	job_id INT NOT NULL AUTO_INCREMENT,
	description CHAR(255),
	PRIMARY KEY (job_id)
)CHARACTER SET utf8;


INSERT INTO Manage
(username, password)
VALUES
('kobi', '$2y$10$ShtbKRSITNOvJerNzIH1dOge22GcM1OELqBhZqkyIqy86f726Lzyu');