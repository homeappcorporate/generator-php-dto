# PHP DTO code generator form OpenAPI spect

## To set up 
```bash
./php composer i
```
## To use 
```shell
./generator create-dto ./examples/publisher.json  
```
# Deploy
## To build image
```shell
docker build . --tag fearofcode/openapi-php-generator --no-cache
```
## To publish
```shell
docker push fearofcode/openapi-php-generator:latest
```
# Development Tools
## To fix code style issues 
`vendor/bin/php-cs-fixer fix`
## To run test
`vendor/bin/phpunit`
## To run static analizer
`vendor/bin/psalm --no-cache`
# TODO
1. Remove minimum-stability: dev when [BackwardCompatibilityCheck](https://github.com/Roave/BackwardCompatibilityCheck) will be released 5.1 version
