#!/bin/sh
RESTARTCMD="/etc/init.d/dovecot start"

PORT=110

PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

LOGFILE="/var/log/watchpop3.log"

DATEFMT="%Y/%m/%d %T"

echo | telnet localhost $PORT 2>&1 | \
grep 'Connection closed by foreign host' > /dev/null || {
   echo "`date +"$DATEFMT"` Failed on port $PORT, restarting..." >> $LOGFILE
   $RESTARTCMD > /dev/null 2>&1  #  if not find 110， then start dovecot
}
