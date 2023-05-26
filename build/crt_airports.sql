drop table airports cascade constraints;

create table airports
	(code varchar(3) PRIMARY KEY,
	 airport_name varchar(75),
	 city varchar(25),
	 airport_state varchar(3)
	);
