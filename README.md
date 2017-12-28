# dksis
# Support student in class register and manage student's performance 

- Installed package
    + postgresql  
	      $ sudo apt-get update
        $ sudo apt-get install postgresql postgresql-contrib
        $ sudo apt-get install pgadmin3 (optional)
    + apache2		
			  $ sudo apt-get update
    		$ sudo apt-get install apache2
    + php       	
			  $ sudo apt-get install php php-pgsql

- Install process
    + git clone	https://github.com/kouhaku111/dksis.git
    + restore database using dksis.backup
    + apache configuration
        $ cd /etc/apache2/sites-available
        $ sudo cp 000-default.cnf dk.conf
        $ sudo chmod -R 777 dk.conf
        $ sudo vi dk.conf
        
           # edit dk.conf
           ServerName dk.dev
           ServerAlias www.dk.dev 
           ServerAdmin admin@example.com
           DocumentRoot /home/tung/dk
           <Directory /home/tung/dk>
              Options Indexes FollowSymLinks
              AllowOverride None
              Require all granted
           </Directory>
          
         $ sudo a2ensite dk.conf (enable dk.conf)
         $ sudo service apache2 reload
         $ sudo vi /etc/hosts
         
            # add line:  
            127.0.0.1 dk.dev
            
- Open browser: localhost://dk.dev
