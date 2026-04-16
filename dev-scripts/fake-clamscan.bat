@echo off
REM dev-scripts\fake-clamscan.bat - simple fake clamscan for local testing
REM Usage: fake-clamscan.bat <file-path>








EXIT /B 0ECHO No threats found)    EXIT /B 1    ECHO %FILE_NAME%: Eicar-Test-Signature FOUNDnSET FILE_NAME=%~nx1
nECHO Fake clamscan scanning %~1
n
nREM If filename contains 'infect' or 'eicar' treat as infected
necho %FILE_NAME% | findstr /i "infect eicar" >nul
nIF %ERRORLEVEL%==0 (