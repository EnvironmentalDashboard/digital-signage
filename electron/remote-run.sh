#!/bin/bash

# `sudo xhost +` needs to be executed on displays' command line for this to work
ssh kiosk1@192.168.1.20 "DISPLAY=:0 nohup media-player"