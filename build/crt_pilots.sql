drop table pilots CASCADE CONSTRAINTS;

create table pilots
	(user_id number(5) PRIMARY KEY REFERENCES users (id));

