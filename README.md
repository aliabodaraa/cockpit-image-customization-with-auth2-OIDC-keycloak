enable Single-Sign-On using OpenID Connect(auth2) for authenticating and authorizing users when they sign in to access digital services- an button added to the signIn page in cockpit to enable the process and redirect the user to keycloak provider rather than provide their credential to cockpit
i deal with two ports 89 and 80 for cockpit and keycloak respectively so to bring up this customization
1.build the image for the docker-compose file
2.run an image to create a new container
3.docker exec -it <container_name_or_id> /bin/bash/var/www/html
4.replace the content of html with the content of the folder in this repo
