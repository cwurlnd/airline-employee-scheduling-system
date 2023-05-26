drop table flightattendants CASCADE CONSTRAINTS;

create table flightattendants
	(user_id number(5) PRIMARY KEY REFERENCES users (id));
