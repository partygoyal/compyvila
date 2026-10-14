<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;

class SubscriberController extends Controller
{
    public function export()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Signed up (IST)'], ',', '"', '');
            foreach (Subscriber::latest('created_at')->cursor() as $subscriber) {
                $row = [$subscriber->email, $subscriber->created_at];
                fputcsv($out, array_map(fn ($value) => preg_match('/^[=+@\-\t\r\n]/', $value) ? "'".$value : $value, $row), ',', '"', '');
            }
            fclose($out);
        }, 'compyvila-subscribers.csv', ['Content-Type' => 'text/csv; charset=utf-8']);
    }
}
