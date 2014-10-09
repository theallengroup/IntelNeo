#/bin/sh

for i in `find $1 |grep php$`;do
	wc -l $i
done

for i in `find $1 |grep htm$`;do
	wc -l $i
done

for i in `find $1|grep css$`;do
	wc -l $i
done
for i in `find $1|grep js$`;do
	wc -l $i
done

for i in `find $1|grep htm$`;do
	wc -l $i
done

