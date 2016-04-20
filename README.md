# Lightium

Super simple CMS organized in a " Category / sub Category / item " hierarchy.

Example can be found at ecrituresnumeriques.ca

To install via docker, you can use the following command

docker run -p 80:80 -p 443:443 -v /backups/folders/lightium-database/:/var/www/data/ -v /opt/git/lightium/src/:/var/www/html/ --name NameOfTheContainer ecrituresnumeriques/lighitum

To install on your server, just copy the src/ folder to your server and execute index.php, the installation script will be invoked

Developped for the Canadian research chair of Digital textualities

Focus for the next version are :
 - Improving the admin interface (rich text editor etc)

v0.1.3 include:
 - Add version table
 - null filter in the API responses
 - add item even if there is no subCat
 - Javascript correction
 - HTML insertion made possible

v0.1.2 include:
 - Insertion / edition of new cat/subcat/item
 - Complete installation script

v0.1.1 include:
- Archive browsing
- SQLite database reader (all INSERT is made by the install.php) with category/subcategory/item working
- Strange taste in UI design
