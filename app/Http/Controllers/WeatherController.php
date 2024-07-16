<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        return view('weather.index');
    }

    public function getWeather(Request $request)
    {
        $city = $request->query('city');
        $days = $request->query('days');
        $apiKey = env('WEATHER_API_KEY');
        $response = Http::get("http://api.weatherapi.com/v1/forecast.json?key={$apiKey}&q={$city}&days={$days}");

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json(['error' => 'Không thể lấy dữ liệu thời tiết'], 500);
    }
}
