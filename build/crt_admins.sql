drop table admins CASCADE CONSTRAINTS;

create table admins
	(user_id number(5) PRIMARY KEY REFERENCES users (id));

