<?php

namespace Crocodicstudio\Crudbooster\helpers;

class Time
{
    private $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

    public function timeAgo($datetime_to, $datetime_from = null, $full = false)
    {
        $now = $this->getNow($datetime_from);
        $ago = new \DateTime($datetime_to);
        $diff = $now->diff($ago);

        $diff->d -= floor($diff->d / 7) * 7;

        foreach ($this->string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k.' '.$v.($diff->$k > 1 ? 's' : '');
            } else {
                unset($this->string[$k]);
            }
        }

        if (! $full) {
            $this->string = array_slice($this->string, 0, 1);
        }

        return $this->string ? implode(', ', $this->string).' ' : 'just now';
    }

    /**
     * @param $datetime_from
     * @return \DateTime
     */
    private function getNow($datetime_from)
    {
        $datetime_from = ($datetime_from) ?: YmdHis();
        $now = new \DateTime;
        if ($datetime_from != '') {
            $now = new \DateTime($datetime_from);
        }

        return $now;
    }
}