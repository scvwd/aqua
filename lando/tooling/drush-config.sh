#!/bin/bash
LANDO_DRUSH=/app/lando/drush
LOCAL_DRUSH=$HOME/.drush
LOCAL_SITES=/app/web/sites/default
echo Create the local config directory
if [ ! -d /app/config/local ]; then
  mkdir /app/config/local
fi
echo Create the private_files directory
if [ ! -d /app/private_files ]; then
  mkdir /app/private_files
fi
echo Add settings.local.php
if [ ! -f $LOCAL_SITES/settings.local.php ]; then
  cp $LOCAL_SITES/lando.settings.php \
  $LOCAL_SITES/settings.local.php
fi
echo Create $LOCAL_DRUSH/sites
if [ ! -d $LOCAL_DRUSH/sites ]; then
  mkdir -p $LOCAL_DRUSH/sites
fi
echo Add drush 8 config
if [ ! -f $LOCAL_DRUSH/drushrc.php ]; then
  cp $LANDO_DRUSH/drushrc.php $LOCAL_DRUSH/drushrc.php
fi
echo Add drush 9 config
if [ ! -f $LOCAL_DRUSH/drush.yml ]; then
  cp $LANDO_DRUSH/drush.yml $LOCAL_DRUSH/drush.yml
fi
echo Add drush 8 aliases
if [ ! -f $LOCAL_DRUSH/scvwd.aliases.drushrc.php ]; then
  cp $LANDO_DRUSH/scvwd.aliases.drushrc.php \
  $LOCAL_DRUSH/scvwd.aliases.drushrc.php
fi
echo Add self.site.yml
if [ ! -f $LOCAL_DRUSH/sites/self.site.yml ]; then
  cp $LANDO_DRUSH/self.site.yml $LOCAL_DRUSH/sites/self.site.yml
fi
