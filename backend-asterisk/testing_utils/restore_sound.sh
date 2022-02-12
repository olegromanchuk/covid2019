#!/bin/bash
cd /usr/share/asterisk/sounds/covid2019/ &&
cp -rf covid_human.wav covid_recorded_human.wav
#sox covid_recorded_human.wav -r 8000 -c1 covid_recorded_human.gsm