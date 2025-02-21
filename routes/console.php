<?php

use App\Console\Commands\SendTodoReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendTodoReminders::class)->everyMinute();
