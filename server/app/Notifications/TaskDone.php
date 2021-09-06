<?php

namespace App\Notifications;
use App\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Notifications\Notification;

class TaskDone extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
       return [TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMessage($notifiable)
    {
		$message='Task '.$notifiable->task_chain->id.' done.'.PHP_EOL;
		$message.='Template:'.$notifiable->task_chain->task->template->name.PHP_EOL;
		$message.='Hashlist:'.$notifiable->task_chain->task->hashlist->name.PHP_EOL;
		
		return 	$message;


    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
