#!/bin/bash

clear;
load_5min=`uptime |  sed 's/.*load average: //' | awk '{print $2}' | sed 's/,//'`;
load_15min=`uptime | sed 's/.*load average: //' | awk '{print $3}' | sed 's/,//'`;
load_average=`echo "(( $load_5min + $load_15min ) / 2)" | bc -l | cut -c 1-4`;
cpu_cores=`cat /proc/cpuinfo | grep 'processor' | wc -l`
cpu_model=`cat /proc/cpuinfo  | grep 'model name'| uniq`
#echo "Load 5 min $load_5min"
#echo "Load 15 min $load_15min"
#echo "CPU Load average $load_average"
#echo "CPU Model: $cpu_model"
#echo "Num of cored $cpu_cores"
#---
if [[ "$cpu_cores" > "$load_average" ]]
then
echo "CPU Load is at <b><font color=green>Normal</font></b> levels"
echo "Load is at $load_average"
fi
#---
if [[ "$cpu_cores" <  "$load_average" ]]
then
echo "<font color = red>CPU Load is above number of cores.</font>"
echo "Load is at $load_aver - Number of cores is $cpu_cores."
#mailx -s "CRITICAL: CPU Load is above number of cores."
fi
#---
if [[ "$cpu_cores" = "$load_average" ]]
then
echo "<font color = yellow>CPU Load is at maximum capacity. Please check</font>"
echo "Load is at $load_aver - Number of cores is $cpu_cores."
#mailx -s "WARNING CPU Load is at maximum capacity. Please check"
fi
exit;


