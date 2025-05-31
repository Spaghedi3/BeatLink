@echo off
REM ────────────────────────────────────────────────────────
REM run_essentia.bat
REM Converts Windows→WSL path, then runs the analyzer
REM ────────────────────────────────────────────────────────

set "WIN_PATH=%~1"

FOR /F "usebackq delims=" %%I IN (`wsl wslpath "%WIN_PATH%"`) DO set "WSL_PATH=%%I"

wsl bash -lc "/home/edi/miniconda/envs/essentia-env/bin/python3 '/mnt/c/Users/edyed/Documents/LICENTA/BeatLink/BeatLink/BeatLink/analyze_audio.py' '%WSL_PATH%'"

exit /b %ERRORLEVEL%
