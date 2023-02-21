#!/usr/bin/env sh

ENV_ARG=${1:-dev}

docker compose exec -e XDEBUG_MODE=off app bin/console --env=$ENV_ARG --force --if-exists doctrine:database:drop &&
docker compose exec -e XDEBUG_MODE=off app bin/console --env=$ENV_ARG doctrine:database:create &&
docker compose exec -e XDEBUG_MODE=off app bin/console --env=$ENV_ARG --no-interaction doctrine:migrations:migrate &&
docker compose exec -e XDEBUG_MODE=off app bin/console --env=$ENV_ARG --no-interaction doctrine:fixtures:load