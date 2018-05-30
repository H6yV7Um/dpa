#!/bin/bash
# This script run at 00:00
# 00 00 * * * /bin/bash  /data0/deve/projects/sh/cut_nginx_log.sh

# The Nginx logs path
logs_path="/data1/logs/"

mkdir -p ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/
chown www:www ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/ -R
mv ${logs_path}adminlogs.log   ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/admin____$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}cj.log   ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/cj_______$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}commentlogs.log ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/comment__$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}nlogs.log       ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/nlogs____$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}taccess.log     ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/t________$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}uibilogs.log    ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/uibi_____$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}unknownlogs.log ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/unknown__$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}wwwlogs.log     ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/www______$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}shenghuo.log    ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/shenghuo_$(date -d "yesterday" +"%Y%m%d").log
mv ${logs_path}yule.log        ${logs_path}$(date -d "yesterday" +"%Y")/$(date -d "yesterday" +"%m")/yule_____$(date -d "yesterday" +"%Y%m%d").log
kill -USR1 `cat /usr/local/webserver/nginx/nginx.pid`
