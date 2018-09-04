#!/bin/bash

(sleep 15; echo "" >> /etc/cron.d/mautic && echo "[INFO] cron has been reconfigured...") &

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
