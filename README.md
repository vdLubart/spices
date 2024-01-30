# Spices REST API

The Spices application provides information about spice storage status at home.
The application is implemented as REST API protected by OAuth2.

The application's REST API described as OpenApi in `.yaml` files format. Each 
separate version described in the separate `.yaml` file with `_vXX` prefix, like
`api_v1.yaml` for API v1.

## API Versioning

The API schema follows the semantic version in format `majorVersion`.`minorVersion`.
`patchVersion`. Only the `majorVersion` changes described in the separate OpenAPI
files. `minorVersion` and `patchVesion` changes do not break backwards compatibility
and just update the existing API file.

### Install Swagger

It is handy to install aside Swagger UI to read and deal with the `api_*.yaml` files.
To install Swagger UI follow the steps described at
https://swagger.io/docs/open-source-tools/swagger-ui/development/setting-up/.

After successful installation, copy (or link) `api_*.yaml` file into 
the new created project. Depending on environment, the root is `dev-helpers` or
`dist` directory. Change the URL in the initializer.js file to navigate to the 
needed source.
