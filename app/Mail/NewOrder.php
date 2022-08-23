<?php

namespace App\Mail;

use App\Models\Orders;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $lang;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Orders $order)
    {
        $this->order = $order;
        $this->lang = $order->orderData['lang'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.new_order_'.$this->lang);
    }
}
