#!/bin/sh

if [ "$*" = off ]
then
sudo service vboxadd-service start
date
elif [ -z "$*" ]
then
sudo service vboxadd-service stop
date
sudo date -s "+ 7 days"
else
sudo service vboxadd-service stop
date
sudo date -s "$*"
fi
echo Starting queue listener
php artisan schedule:run
php artisan queue:listen > /dev/null 2>&1 &
