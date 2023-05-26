drop table attends CASCADE CONSTRAINTS;

create table attends
	(fa_id number(5) REFERENCES flightattendants(user_id),
	 flight_id number(7) REFERENCES flights(id),
	 curr_status varchar(1)
	);	