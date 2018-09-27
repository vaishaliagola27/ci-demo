#!/usr/bin/env bash

set -e
#service mysql start
source "$PROJECT_ROOT/.circleci/setup_env.sh"
setup_environment

mkdir -p "$build_root"
pushd "$build_root" > /dev/null
wp core download  --allow-root
mv "$project_root/.circleci/.env.travis" "$build_root/.env"

sed --quiet "s/^DB_NAME=.*/DB_NAME=$DB_NAME/" "$build_root/.env"
sed --quiet "s/^DB_USER=.*/DB_USER=$DB_NAME/" "$build_root/.env"
sed --quiet "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" "$build_root/.env"
sed --quiet "s/^DB_HOST=.*/DB_HOST=$DB_HOST/" "$build_root/.env"

mv "$project_root/.circleci/wp-config.php" "$build_root/wp-config.php"
mv "$project_root/vendor" "$build_root/"

# waiting for mysql to start if not started yet!
dockerize -wait tcp://localhost:3306 -timeout 1m

wp core install --url=example.com --title=CI --admin_user=ci --admin_password=blahblahblah --admin_email=ci@example.com --allow-root
rm -rf "$build_root/wp-content/themes/"{twentyfifteen,twentysixteen}
wp plugin activate --all --allow-root
wp eval 'echo "wp verify success";' --allow-root
rm .env
mkdir -p "$build_root/wp-content"
ls -ltr "$project_root/.circleci/"
rsync -azeh --exclude ".git/" "$project_root/" "$build_root/wp-content/"
pushd "$build_root/wp-content" > /dev/null
rm -r uploads && ln -sn ../../../uploads
popd > /dev/null
popd > /dev/null
