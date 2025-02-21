<?php

namespace App\Mail;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable {
    use Queueable, SerializesModels;

    public $todo;
    public $csvFilePath;

    public function __construct(Todo $todo, $csvFilePath) {
        $this->todo        = $todo;
        $this->csvFilePath = $csvFilePath;
    }

    /**
     * Build the message.
     */
    public function build() {
        return $this->subject('Reminder for Todo: ' . $this->todo->title)
            ->view('emails.reminder')
            ->attach($this->csvFilePath, [
                'as'   => 'titles.csv',
                'mime' => 'text/csv',
            ]);
    }
}
