drop table planes CASCADE CONSTRAINTS;

create table planes
	(tail_num varchar(7) PRIMARY KEY,
	 seats number(3),
     manuf varchar(50)
	);	
