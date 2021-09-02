# A skeleton project
## How to use
`composer create-project homeapp/php-skeleton repositry-name`



# After creating project
Change in `composer.json`
```
"name":
"description": 
"Homeapp\\LibraryOrProjectName\\":
```
Change the README.md

# Tools
## To fix code style issues 
`vendor/bin/php-cs-fixer fix`
## To run test
`vendor/bin/phpunit`
## To run static analizer
`vendor/bin/psalm --no-cache`
# TODO
1. Remove minimum-stability: dev when [BackwardCompatibilityCheck](https://github.com/Roave/BackwardCompatibilityCheck) will be released 5.1 version
