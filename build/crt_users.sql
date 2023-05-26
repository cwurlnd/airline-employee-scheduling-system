drop table users CASCADE CONSTRAINTS;
drop sequence users_seq; 

create table users
	(id number(5) PRIMARY KEY,
	 email varchar(50),
	 password varchar(20),
	 first_name varchar(50),
	 last_name varchar(50)
	);	

create sequence users_seq START WITH 1;
