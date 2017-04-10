# Lightium

Super simple CMS organized in a " Category / sub Category / item " hierarchy.

Example can be found at ecrituresnumeriques.ca

To install via docker, you can use the following command

    docker run -d -p 80:80 -p 443:443 -v /backup/folder/:/var/www/data/ --name Lightium ecrituresnumeriques/lightium

To install multiple lightium, you can use the following command in addition to using github to update the code

    docker run -d -p 80:80 -p 443:443 -v /backups/folders/lightium-database/:/var/www/data/ -v /opt/git/Lightium/src/:/var/www/html/ --name Lightium -e VIRTUAL_HOST=domain.com ecrituresnumeriques/lightium

To install on your server, just copy the src/ folder to your server and execute index.php, the installation script will be invoked

Developped for the Canadian research chair of Digital textualities

## Guidelines for v1:
The proof of concept (v0) revealed few limitations of a naive category/subcategory/item limit, as well as improvment for futur releases:
- Need to support semantic informations of category/subcategory (especially for authors, type of text)
- Need to support deeper templating of the website + customization
- Tools for easy plugin developpement
- webhooks (in and out) on update
 
The proposed solutions are for now to split backend and front end, and give more freedom to the user on the front-end side.
- backend sails.js for the API side
- react + redux frontend, associated with RxJS for communication between front and back-end
- make sure react classes are easily overwritable to support custom element.


## Focus for the next version are :
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
