# Create user : 'intramessenger' (database is 'IntraMessenger')
# with default password : 'thepassword' (change it before execute !)
# Serveur is localhost
#

CREATE USER 'intramessenger'@'localhost' IDENTIFIED BY 'thepassword';

# GRANT USAGE ON * . * TO intramessenger@'localhost' IDENTIFIED BY 'thepassword' ;
# REVOKE ALL PRIVILEGES ON `IntraMessenger` . * FROM "intramessenger"@"localhost";
# REVOKE GRANT OPTION ON `IntraMessenger` . * FROM "intramessenger"@"localhost";

GRANT SELECT , INSERT , UPDATE , DELETE , CREATE ,
	INDEX ON `IntraMessenger` . * TO "intramessenger"@"localhost";
