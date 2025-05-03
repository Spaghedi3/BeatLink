@echo off
set "ESSENTIA_INPUT=%1"
wsl /home/edi/miniconda/condabin/conda run -n essentia-env python3 /mnt/c/Users/edyed/Documents/LICENTA/BeatLink/BeatLink/BeatLink/analyze_audio.py %ESSENTIA_INPUT%
