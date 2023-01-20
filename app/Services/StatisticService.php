<?php


namespace App\Services;


use App\Models\Statistics;
use Illuminate\Http\Request;

class StatisticService
{


    public static function addItem(Request $request, $event, $note = null, $order_id = null, $gid = null)
    {

        $botAgents = [
            'PetalBot',
            'Googlebot',
            'YandexMetrika',
            'bingbot',
        ];

        $user_agent = $request->header('user-agent');

        foreach ($botAgents as $k => $bot) {
            if (preg_match("/$bot/", $user_agent)) {
                return true;
            }
        }

        $url = $request->getRequestUri();
        $get_param = null;
        $utm_id = null;
        $order_id = session('order_id');

        preg_match('/\?.*/', $url, $mach);

        if (isset($mach[0])) {
            $get_param = $mach[0];
            $url = str_replace($get_param, '', $url);
        }


        $post = $request->post();

        if (!$order_id && isset($post['order_id'])) {
            $order_id = $post['order_id'];
        }

        if ($url != '/404') {
            $statistic = new Statistics();
            $statistic->ip = $request->getClientIp();
            $statistic->sid = session()->getId();
            $statistic->url = $url;
            $statistic->get_param = $get_param;
            $statistic->event = $event;
            $statistic->note = "$user_agent || $note";
            $statistic->order_id = $order_id;
            $statistic->gid = $gid;
            $statistic->utm_id = session('utm_id');

            $statistic->save();
        }

    }

    public static function getStatistics($date_from, $date_to)
    {
        $query = Statistics::whereBetween('created_at', [$date_from, $date_to]);
        $query_st = Statistics::whereBetween('created_at', [$date_from, $date_to]);

        $unic_sid = $query->distinct()->pluck('sid')->count();
        $statistick['clients_count'] = $unic_sid;

        $data = $query->distinct()->pluck('url')->toArray();
        foreach ($data as $item) {
            $count = $query_st->where('url', $item)->count();
            $statistick['pages'][$item] = $count;
        }

        $data = $query->distinct()->pluck('event')->toArray();
        foreach ($data as $item) {
            $count = $query_st->where('event', $item)->count();
            $statistick['events'][$item] = $count;
        }

        $data = $query->distinct()->pluck('note')->toArray();
        foreach ($data as $item) {
            $count = $query_st->where('note', $item)->count();
            $statistick['notes'][$item] = $count;
        }


        return $statistick;
    }
}