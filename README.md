PHP CHALLENGE
=========

Brief Description:
Your customer receives two XML models from his partner. This information must be
available for both web system and mobile app.

The challenge:
Create a Symfony2 application to manually upload the given XMLs and have an option
to process them. Make the processed information available via rest APIs.

Must have:
1. Symfony2, doctrine, composer, mysql
1. An index page to upload the XML with a button to process it.
1. Rest APIs for the XML data, only the GET methods are needed.
1. README.md with the instructions to install the application (docker is a plus here :) )

Bonus points:
1. Drag and drop upload component.
1.  Authentication method to the APIs.
1.  Generated documentation for the APIs.
1.  Unit/Functional tests.

### To run the project you need to: ###

1. run composer install
1. fill database parameters info via terminal
1. or cp app/config/parameters.yml.dist parameters.yml edit parameters.yml to set your mysql user, password and database name
1. run php app/console doctrine:database:create
1. run php app/console doctrine:schema:create
1. run php app/console server:start
1. open your browser and go to 127.0.0.1:8000

### APIDOC: ###

http://127.0.0.1:8000/api/doc

