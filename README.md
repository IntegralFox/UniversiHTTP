# UniversiHTTP
A web application to simplify the creation, submission, and securing of web development class assignments.

## Dependencies
A LAMP stack that satisifies the following:
* A web server capable of fastCGI and preferably secured with an SSL certificate (NGiNX recommended)
* PHP 5.4 or newer
* A PDO-compatible relational database (MySQL tested. Should be PostgreSQL compatible.)

## Installation
* Clone this repository into a webserver root
* Create a least privilege MySQL user for the application and create the database with the included SQL script
* Configure the webserver to pass all URLs except those to /static/* to the main application file (example below)
* Configure the secureConstants.php file with the authentication details of the SQL server
* Log into the site with the default user `instructor` and password `password`, change the password, and create faculty accounts

## Sample NGiNX Server Blocks
```
server {
  # Redirect HTTP to HTTPS connection
  listen 80;
  server_name sub.domain.tld;
  return 301 https://$server_name$request_uri;
}
server {
  listen 443;
  server_name sub.domain.tld;
  root /srv/http/tld/domain/sub;

  ssl on;
  ssl_certificate /etc/nginx/server.crt;
  ssl_certificate_key /etc/nginx/server.key;

  location /static {
    index index.html;
  }

  location / {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/script/universihttp.php;
    fastcgi_pass 127.0.0.1:9000;
  }
}
```
