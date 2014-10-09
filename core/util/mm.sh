#/usr/bin/sh

./m.sh $1 >test11.txt
#gawk  '{print $0}' text11.txt
#gawk -f' ' '{print $1}'
gawk -F' ' -f mine.awk ./test11.txt
