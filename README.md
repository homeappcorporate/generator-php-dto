# PHP DTO code generator form OpenAPI spect

## To set up 
```bash
./php composer i
```
## To use 
```shell
./generator create-dto ./examples/publisher.json  
```
### or via image 


# Development Tools
## To fix code style issues 
`vendor/bin/php-cs-fixer fix`
## To run test
`vendor/bin/phpunit`
## To run static analizer
`vendor/bin/psalm --no-cache`
# TODO
- [ ] Support allOf, onOff, anyOf
- [ ] Discriminator
- [ ] Enums
- [ ] SDK client generation
- [ ] Image tag generation automation
- [ ] Extention for codeception (in separate repository)



## Run for project
Clients
```shell
./clients.sh                                                                                                                                                          
```