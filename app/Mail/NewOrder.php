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
    public $shop_setting;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Orders $order, $shop_setting)
    {
        $this->order = $order;
        $this->shop_setting = $shop_setting;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.new_order');
    }
}
