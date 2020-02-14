#!/bin/bash
# make sure we can write to docroot/sites/default
sudo chmod -R ug+w /app/web/sites/default
# make sure docroot/sites/default is owned by www-data
sudo chown -R www-data:www-data /app/web/sites/default
# pull down the latest code changes
git pull origin develop
# fix file permissions
if [ ! -d ~/.drush/file_permissions ]; then
  drush dl file_permissions --destination=~/.drush
  drush cc drush
fi
drush fp
