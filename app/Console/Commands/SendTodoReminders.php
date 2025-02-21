<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderEmailJob;
use App\Models\Todo;
use Illuminate\Console\Command;

class SendTodoReminders extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch reminder email jobs for todos due in 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle() {
        $todos = Todo::where('reminder_email_sent', false)
            ->where('due_date', '<=', now()->addMinutes(10))
            ->where('due_date', '>', now())
            ->get();

        foreach ($todos as $todo) {
            dispatch(new SendReminderEmailJob($todo));
        }

        $this->info('Reminder email jobs dispatched for due todos.');
    }
}
