<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WeatherController extends Controller
{
    public function index()
    {
        return view('weather.index');
    }
    public function filterWeather(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'city' => 'required'
            ], [
                'city.required' => 'The city field is required.'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $city = $request->input('city');
            $apiKey = 'e007277ac91a4ff88ad83238252602';
            $url = "http://api.weatherapi.com/v1/current.json?key={$apiKey}&q={$city}";

            $client = new Client(); 
            $response = $client->get($url);
            $weatherData = json_decode($response->getBody(), true);

            $formattedData = [
                'city' => $weatherData['location']['name'],
                'current_temp' => $weatherData['current']['temp_c'],
                'feels_like' => $weatherData['current']['feelslike_c'],
                'country' => $weatherData['location']['country'],
                'description' => $weatherData['current']['condition']['text'],
                'icon' => $weatherData['current']['condition']['icon']
            ];

            return response()->json($formattedData);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
