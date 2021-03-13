<?php
namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Telegram;
class TelegramChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toMessage($notifiable);
        $telegram = Telegram::where('enabled', '1')->first();
        if ($telegram != null)
        {

            try
            {
                $response = Http::retry(3, 100)->post('https://api.telegram.org/bot' . $telegram->token . '/sendMessage', ['chat_id' => $telegram->chat_id, 'text' => $message, ]);
            }
            catch(\Illuminate\Http\Client\RequestException $e)
            {
                //todo
                
            }
        }

    }
}

