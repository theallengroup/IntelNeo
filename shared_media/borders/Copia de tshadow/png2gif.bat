@echo off
set dir=jorge
set initial_format=png
set target_format=gif
for /f %%Q in (images2.txt) do (
	convert %dir%\%%Q.%initial_format% %dir%\%%Q.%target_format% 
)
