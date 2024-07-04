<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ExpiredDocumentsNotification;
use Illuminate\Console\Command;

class SendReminderOfExpiringDocumentsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminder-of-expiring-documents-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to users notifying them of documents expiring soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usersWithExpiringDocuments = User::whereHas('documents', function ($query) {
            $query->expired()->orWhere(function ($query) {
                $query->expiringSoon();
            });
        })->with(['documents' => function ($query) {
            $query->expired()->orWhere(function ($query) {
                $query->expiringSoon();
            });
        }])->get();

        foreach ($usersWithExpiringDocuments as $user) {
            $user->notify(new ExpiredDocumentsNotification(
                expiringDocuments: $user->documents
            ));
        }
    }
}
