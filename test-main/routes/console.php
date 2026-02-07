<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reservations:complete')->dailyAt('03:00');