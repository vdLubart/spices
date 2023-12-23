# Spices @ Home

Application provides information about spices storage status at home.

## REST API

The application provides the REST API for all the needed interactions. The 
full list of endpoints may be found in `api.yaml` swagger doc.

### Install Swagger

It is handy to use Swagger UI to read and deal with the `api.yaml` file.
To install Swagger UI follow the steps described at
https://swagger.io/docs/open-source-tools/swagger-ui/development/setting-up/.

After successful installation, copy (or link) `api.yaml` file into 
`./swagger-ui/dev-helpers` directory and change the URL in line 7 of
`./swagger-ui/dev-helper/dev-helper-initializer.js` to
`http://localhost:3200/api.yaml`