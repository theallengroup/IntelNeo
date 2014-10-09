#/usr/bin/sh
#
#USAGE: ./find.sh .
#to count all the lines in the dir.
#echo "enter:$1"
#echo " "



for filename in `ls -l $1|grep "^-r.*php$"|gawk -F' ' '{print $9}'` ;do
	echo $filename
	#wc -l $filename
done;

#recurse all dirs!
for l in `ls -l "$1" |grep "^d.*"|gawk -F' ' '{print $9}'`;do 
#	echo "atemting to enter:$1/$l"
	cd $l
	./find.sh $1/$l;
	cd ..
done





#ls -lR >allfiles.txt
#grep "^-rw.*php$" allfiles.txt > allphpfiles.txt
#gawk -F' ' '{print $9}' allphpfiles.txt >allfilesnoext.txt

#$1 represents a folder name.
#[$# == 0 ] || [echo "missing: dirname.";exit]

#for l in ;do
#	echo "test: $l"
#done
	
