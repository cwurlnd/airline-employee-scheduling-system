sqlplus -S timmy/timmy @create
sqlldr timmy/timmy control=airports.ctl
sqlldr timmy/timmy control=users.ctl
sqlldr timmy/timmy control=pilots.ctl
sqlldr timmy/timmy control=flightattendants.ctl
sqlldr timmy/timmy control=admins.ctl
sqlldr timmy/timmy control=planes.ctl
sqlldr timmy/timmy control=flights.ctl
