<?php

use App\Console\Commands\SendTodoReminders;

Schedule::command(SendTodoReminders::class)->everyMinute();
