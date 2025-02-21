<?php

namespace App\Jobs;

use App\Mail\ReminderEmail;
use App\Models\EmailLog;
use App\Models\Todo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendReminderEmailJob implements ShouldQueue {
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $todo;

    public function __construct(Todo $todo) {
        $this->todo = $todo;
    }

    public function handle() {
        Log::info("Starting SendReminderEmailJob for Todo ID: {$this->todo->id} (Email: {$this->todo->email})");

        $response = Http::get('https://jsonplaceholder.typicode.com/posts');
        if ($response->successful()) {
            $posts  = $response->json();
            $titles = array_map(function ($post) {
                return $post['title'];
            }, array_slice($posts, 0, 10));
        } else {
            EmailLog::create([
                'todo_id'   => $this->todo->id,
                'subject'   => 'Reminder Email',
                'recipient' => $this->todo->email,
                'message'   => 'Failed to fetch posts for CSV attachment.',
                'status'    => 'failed',
            ]);
            Log::error("Failed to fetch posts from API for Todo ID: {$this->todo->id}");
            return;
        }

        $csvData = "Title\n";
        foreach ($titles as $title) {
            $csvData .= '"' . str_replace('"', '""', $title) . "\"\n";
        }

        $csvFileName = 'csv_' . time() . '.csv';
        Storage::disk('local')->put($csvFileName, $csvData);
        $csvFilePath = Storage::disk('local')->path($csvFileName);

        try {
            Mail::to($this->todo->email)->send(new ReminderEmail($this->todo, $csvFilePath));

            EmailLog::create([
                'todo_id'   => $this->todo->id,
                'subject'   => 'Reminder Email',
                'recipient' => $this->todo->email,
                'message'   => 'Reminder email sent successfully with CSV attachment.',
                'status'    => 'sent',
            ]);
            Log::info("Reminder email successfully sent for Todo ID: {$this->todo->id}");

            $this->todo->update(['reminder_email_sent' => true]);
        } catch (Exception $e) {
            EmailLog::create([
                'todo_id'   => $this->todo->id,
                'subject'   => 'Reminder Email',
                'recipient' => $this->todo->email,
                'message'   => 'Email sending failed: ' . $e->getMessage(),
                'status'    => 'failed',
            ]);
            Log::error("Email sending failed for Todo ID: {$this->todo->id} with error: " . $e->getMessage());
        }

        Storage::disk('local')->delete($csvFileName);
        Log::info("Temporary CSV file deleted for Todo ID: {$this->todo->id}");
    }
}
