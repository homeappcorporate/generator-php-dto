#!/bin/bash
#firstArgument=$1
#firstCharacter="${firstArgument%"${firstArgument#?}"}"
#if [ "$firstCharacter" = "-" ];  then
#  COMMAND="php ${@}"
#else
  COMMAND="/var/www/setup/install-composer && ${@}";
#fi
docker run --rm -it \
 -v $(pwd)/:/var/www \
 -v temp-composer:/composer \
 -v tmp-setup:/tmp/setup/ \
  -w /var/www \
  --entrypoint="/bin/sh" \
  php:7.4-cli-alpine3.13 -c "${COMMAND}"