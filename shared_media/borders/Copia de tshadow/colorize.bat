
@echo off
rem 151,187,240
set color="rgb(151,187,240)"
set color="rgb(222,238,254)"
set input_dir=lightblue
set output_dir=light2
set source_dir=round
set final=jorge

mkdir %output_dir%
mkdir %final%
for /f %%Q in (images.txt) do (
	echo %%Q	
	convert %input_dir%\%%Q -fill %color% -draw "rectangle 0,0,100,100 " %output_dir%\%%Q
	composite -gravity center %source_dir%\%%Q %output_dir%\%%Q  %final%\%%Q
	del %output_dir%\%%Q
)
rmdir %output_dir%

