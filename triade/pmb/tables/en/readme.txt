-- +-------------------------------------------------+
-- © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
-- +-------------------------------------------------+
-- $Id: readme.txt,v 1.8 2021/05/03 10:13:11 dbellamy Exp $

------------------------------------------------------------------------------------------------------------------

Description of the files
bibli.sql : structure of the database only, no data

minimum.sql : admin/admin user, application parameters

feed_essential.sql : this is what you need to use the application in quick-start mode: 
	Preliminary, modifyable application data
	A set of backups ready to use
	A set of z3950 parameters.
	
data_test.sql : A small selection of data containing volumes, borrowers, allowing you to test the PMB suite.
	Volumes, borrowers, lenders, compies, serials
	Based on the application data found in feed_essential.sql
	Must load the thesaurus UNESCO_FR unesco_fr.sql
	
Thesaurus : 3 thesaurus are provided for you:
	unesco_fr.sql : UNESCO's hierarchical thesaurus, important enough and done well.
	agneaux.sql : smaller, simpler but also well done.
	environnement : a thesaurus potentially for use in an environmental library.
	
Internal indices: 4 indices are provided:
	indexint_small_en.sql : Reduced Dewey style index in English
	indexint_100.sql : 100 cases of knowlege, or a colour marguerite flower, Dewey decimal style index
	simplified for education
	indexint_chambery.sql : Dewey style index from the Chambéry library, very well conceived.
	but can be adapted for small libraries
	indexint_dewey.sql : Dewey style index


************************************************************************************************
________________________________________________________________________________________________
Attention, if you carry out an update from an existing database:
------------------------------------------------------------------------------------------------
*********** To do following each installation or application update ****************
When you install a new version
over a previous version, you must vitally,
after copying the files contained in this archive
onto the web server:

check that the parameters contained in :
./includes/db_param.inc.php
./opac_css/includes/opac_db_param.inc.php

correspond to your configuration (do a backup before you start!)

Moreover:
You must do the core update of the database.
Nothing will be lost.

Connect in your normal way to PMB, the graphical style can be different, even absent (display is usable enough without colour or images)

Go to Administration > Utils > update database to put your core database up to date  

A series of messages will indicate the successive updates, 
To continue the database update, use the link at the bottom of the page just after 
'Your database is at version...' is displayed.

You can then edit your account to eventually update
your preferences, in particular your graphical style.

Don't hesitate to tell us about your problems or ideas 
by email : pmb@sigb.net

Moreover, we would be happy to count you among our users and
some figures such as number of readers, items, CDs... with the 
location of your establishment (or specific name) would be enough for us

to get to know you better.

More information in the folder ./doc or also
on our website http://www.sigb.net

The development team

