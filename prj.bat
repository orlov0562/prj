@echo  off

SET PHP=php

SET PRJCMD=%PHP% %~dp0scripts\prj.php "%*"

%PRJCMD%

if %ERRORLEVEL% EQU 1 (
	FOR /F "usebackq delims=|" %%i IN (`%PRJCMD%`) DO (
		%%i
	)
)