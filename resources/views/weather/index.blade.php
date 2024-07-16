<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển Thời Tiết</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.9/css/weather-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
        }

        .weather-dashboard {
            width: 600px;
            background-color: #f1f8ff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .header {
            background-color: #3f51b5;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .search-container {
            display: flex;
            justify-content: center;
            padding: 20px;
            background-color: #e8eaf6;
        }

        .search-container input {
            width: 80%;
            padding: 10px;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        .search-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 0 5px 5px 0;
            background-color: #3f51b5;
            color: white;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #303f9f;
        }

        .current-weather {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background-color: #c5cae9;
        }

        .weather-details {
            display: flex;
            flex-direction: column;
        }

        .weather-icon {
            font-size: 50px;
        }

        .forecast {
            background-color: #e8eaf6;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .forecast-day {
            background-color: #c5cae9;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            width: 21%;
            margin-bottom: 10px;
        }

        #loadMoreBtn {
            width: 100%;
            padding: 10px;
            background-color: #3f51b5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            display: none;
        }

        #loadMoreBtn:hover {
            background-color: #303f9f;
        }

        .subscription {
            background-color: #e8eaf6;
            padding: 20px;
            text-align: center;
        }

        .subscription input {
            width: 80%;
            padding: 10px;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
            margin-right: 5px;
        }

        .subscription button {
            padding: 10px 20px;
            border: none;
            border-radius: 0 5px 5px 0;
            background-color: #3f51b5;
            color: white;
            cursor: pointer;
        }

        .subscription button:hover {
            background-color: #303f9f;
        }
    </style>
</head>

<body>
    <div class="weather-dashboard">
        <div class="header">
            Weather Dashboard
        </div>
        <div class="search-container">
            <input type="text" id="city" placeholder="E.g., New York, London, Tokyo">
            <button onclick="getWeather()">Search</button>
        </div>
        <div class="current-weather" id="currentWeather">
            <!-- Thời tiết hiện tại sẽ được hiển thị ở đây -->
        </div>
        <div class="forecast" id="forecast">
            <!-- Dự báo thời tiết sẽ được hiển thị ở đây -->
        </div>
        <button id="loadMoreBtn" onclick="loadMore()">Load more</button>
        <div class="subscription">
            <h3>Đăng ký nhận thông tin thời tiết hàng ngày</h3>
            <input type="email" id="email" placeholder="Email của bạn" required>
            <button onclick="subscribe()">Đăng ký</button>
            <button onclick="unsubscribe()">Hủy đăng ký</button>
        </div>
    </div>

    <script>
        let forecastData = [];
        let displayedDays = 5;
        let totalDays = 0;

        function getWeather() {
            const city = document.getElementById('city').value;
            let storedData = sessionStorage.getItem(city);
            if (!storedData) {
                fetch(`/weather?city=${city}&days=${displayedDays + 7}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            document.getElementById('currentWeather').innerHTML = '<p>' + data.error + '</p>';
                            document.getElementById('forecast').innerHTML = '';
                            document.getElementById('loadMoreBtn').style.display = 'none';
                        } else {
                            forecastData = data.forecast.forecastday;
                            totalDays = forecastData.length;
                            sessionStorage.setItem(city, JSON.stringify(data))
                            displayWeather(data, forecastData.slice(0, displayedDays));

                            if (totalDays > displayedDays) {
                                document.getElementById('loadMoreBtn').style.display = 'block';
                            } else {
                                document.getElementById('loadMoreBtn').style.display = 'none';
                            }
                        }

                    });
            } else {
                let data = JSON.parse(storedData)
                forecastData = data.forecast.forecastday;
                totalDays = forecastData.length;
                displayWeather(data, forecastData.slice(0, displayedDays));

                if (totalDays > displayedDays) {
                    document.getElementById('loadMoreBtn').style.display = 'block';
                } else {
                    document.getElementById('loadMoreBtn').style.display = 'none';
                }
            }
        }

        function loadMore() {
            displayedDays += 4;
            displayWeather(null, forecastData.slice(0, displayedDays));
            if (displayedDays >= totalDays) {
                document.getElementById('loadMoreBtn').style.display = 'none';
            }
        }

        function displayWeather(data, forecast) {
            const currentWeatherElem = document.getElementById('currentWeather');
            const forecastElem = document.getElementById('forecast');

            if (data) {
                currentWeatherElem.innerHTML = `
                    <div>
                        <h2>${data.location.name} (${data.location.localtime})</h2>
                        <p>Temperature: ${data.current.temp_c}°C</p>
                        <p>Wind: ${data.current.wind_kph} km/h</p>
                        <p>Humidity: ${data.current.humidity}%</p>
                        <p>${data.current.condition.text}</p>
                    </div>
                    <i class="wi ${getWeatherIcon(data.current.condition.code)} weather-icon"></i>
                `;
            }

            let forecastHTML = '';
            let check = false;
            forecast.forEach(day => {
                if (!check) check = true;
                else {
                    forecastHTML += `
                        <div class="forecast-day">
                            <p>${day.date}</p>
                            <i class="wi ${getWeatherIcon(day.day.condition.code)} weather-icon"></i>
                            <p>Temperature: ${day.day.avgtemp_c}°C</p>
                            <p>Wind: ${day.day.maxwind_kph} km/h</p>
                            <p>Humidity: ${day.day.avghumidity}%</p>
                            <p>${day.day.condition.text}</p>
                        </div>
                    `;
                }
            });

            forecastElem.innerHTML = forecastHTML;
        }

        function getWeatherIcon(conditionCode) {
            const icons = {
                1000: 'wi-day-sunny',
                1003: 'wi-cloudy',
                1006: 'wi-cloud',
                1009: 'wi-cloudy',
                1030: 'wi-fog',
                1063: 'wi-showers',
                1066: 'wi-snow',
                1069: 'wi-sleet',
                1072: 'wi-snow',
                1087: 'wi-thunderstorm',
                1114: 'wi-snow',
                1117: 'wi-snow-wind',
                1135: 'wi-fog',
                1147: 'wi-fog',
                1150: 'wi-showers',
                1153: 'wi-showers',
                1168: 'wi-rain-mix',
                1171: 'wi-rain-mix',
                1180: 'wi-showers',
                1183: 'wi-showers',
                1186: 'wi-rain',
                1189: 'wi-rain',
                1192: 'wi-rain',
                1195: 'wi-rain',
                1198: 'wi-rain-mix',
                1201: 'wi-rain-mix',
                1204: 'wi-sleet',
                1207: 'wi-sleet',
                1210: 'wi-snow',
                1213: 'wi-snow',
                1216: 'wi-snow',
                1219: 'wi-snow',
                1222: 'wi-snow',
                1225: 'wi-snow',
                1237: 'wi-hail',
                1240: 'wi-showers',
                1243: 'wi-rain',
                1246: 'wi-rain',
                1249: 'wi-sleet',
                1252: 'wi-sleet',
                1255: 'wi-snow',
                1258: 'wi-snow',
                1261: 'wi-hail',
                1264: 'wi-hail',
                1273: 'wi-storm-showers',
                1276: 'wi-thunderstorm',
                1279: 'wi-storm-showers',
                1282: 'wi-thunderstorm'
            };

            return icons[conditionCode] || 'wi-na';
        }

        function subscribe() {
            const email = document.getElementById('email').value;
            fetch('/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("CHECK DATA: ", data);
                    if (data.success) {
                        alert('Đã gửi email xác nhận đăng ký. Vui lòng kiểm tra hộp thư!');
                    } else {
                        alert('Vui lòng nhập email!');
                    }
                });
        }

        function unsubscribe() {
            const email = document.getElementById('email').value;
            fetch('/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã gửi email xác nhận hủy đăng ký. Vui lòng kiểm tra hộp thư!');
                    } else {
                        alert('Đã có lỗi xảy ra: ' + data.message);
                    }
                });
        }
    </script>
</body>

</html>
