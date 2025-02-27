<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExcjRhYXB3d2tlbGVzNDl2MXR0aDV1bWx2OWo3azh4MjN5ZW40dDYweSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3oEjHGZkrolm9UgvM4/giphy.gif') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .search-box {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            padding: 0.75rem;
            border: none;
            border-radius: 25px 0 0 25px;
            outline: none;
            width: 70%;
            font-size: 1rem;
        }

        .search-box button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0 25px 25px 0;
            background: #ff6f61;
            color: #fff;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .search-box button:hover {
            background: #ff4a3d;
        }

        .weather-info {
            display: none;
            /* Hidden by default */
        }

        .weather-info h2 {
            font-size: 2rem;
            margin: 1rem 0;
        }

        .weather-info img {
            width: 100px;
            height: 100px;
        }

        .weather-info p {
            font-size: 1.2rem;
            margin: 0.5rem 0;
        }

        .error {
            color: #ff4a3d;
            font-size: 1rem;
            margin-top: 1rem;
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            h1 {
                font-size: 2rem;
            }

            .search-box input {
                width: 60%;
            }

            .search-box button {
                padding: 0.75rem 1rem;
            }

            .weather-info h2 {
                font-size: 1.5rem;
            }

            .weather-info p {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Weather App</h1>
        <div class="search-box">
            <input type="text" id="city-input" placeholder="Enter city name">
            <button id="search-btn">Search</button>
        </div>
        <div class="weather-info" id="weather-info">
            <h2 id="city-name"></h2>
            <img id="weather-icon" src="" alt="Weather Icon">
            <p id="temperature"></p>
            <p id="weather-description"></p>
        </div>
        <div class="error" id="error-message"></div>
    </div>

    <script>
        const apiKey = 'e007277ac91a4ff88ad83238252602';
        const searchBtn = document.getElementById('search-btn');
        const cityInput = document.getElementById('city-input');
        const weatherInfo = document.getElementById('weather-info');
        const errorMessage = document.getElementById('error-message');

        searchBtn.addEventListener('click', () => {
            const city = cityInput.value.trim();
            if (city) {
                fetchWeather(city);
            } else {
                showError('Please enter a city name.');
            }
        });

        async function fetchWeather(city) {
            const apiUrl = `http://api.weatherapi.com/v1/current.json?key=${apiKey}&q=${city}`;

            try {
                const response = await fetch(apiUrl);
                if (!response.ok) throw new Error('City not found.');
                const data = await response.json();
                console.log(data);
                // Update DOM with weather data
                document.getElementById('city-name').textContent = data.location.name;
                document.getElementById('temperature').textContent = `${Math.round(data.current.temp_c)}°C`;
                document.getElementById('weather-description').textContent = data.current.condition.text;
                document.getElementById('weather-icon').src =
                    `https:${data.current.condition.icon}`;

                // Show weather info and hide error
                weatherInfo.style.display = 'block';
                errorMessage.style.display = 'none';
            } catch (error) {
                showError('City not found. Please try again.');
                weatherInfo.style.display = 'none';
            }
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }
    </script>
</body>

</html>
