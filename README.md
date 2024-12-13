# тестовое задание для perfect-panel

# Задание 1
```
SELECT 
    u.id AS ID, 
    CONCAT(u.first_name, ' ', u.last_name) AS Name,
    b.author AS Author,
    GROUP_CONCAT(b.name ORDER BY b.id SEPARATOR ', ') AS Books
FROM users AS u
JOIN user_books AS ub ON u.id = ub.user_id
JOIN books AS b ON ub.book_id = b.id
WHERE TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) >= 7
  AND TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) <= 17
  AND DATEDIFF(ub.return_date, ub.get_date) <= 14
GROUP BY u.id, u.first_name, u.last_name, b.author
HAVING COUNT(DISTINCT b.id) = 2 
   AND COUNT(DISTINCT b.author) = 1;
```

# Задание 2
- 1 Установка проекта
1) выолпнить git clone проекта
2) открыть проект через консоль
3) cd docker
4) docker-compose up -d  
5) docker exec -it docker-app-1 bash
6) composer install
7) php artisan migrate (опционально, так как без этого http://localhost:8080/ не откроется. Но запросы можно будет делать)

- 2 Проверка работы АПИ
1) 
```
    curl --location 'http://localhost:8080/api/v1/rates'
```
2) 
```
    curl --location 'http://localhost:8080/api/v1/rates?currency=BTC%2CUSD' \
    --header 'Authorization: Bearer e8EA-8DLq-9Pz6P_mMBHs3w4VHEU89Q_MrFweU1-WkR'
```
3) 
```curl --location 'http://localhost:8080/api/v1/rates' \
--header 'Authorization: Bearer e8EA-8DLq-9Pz6P_mMBHs3w4VHEU89Q_MrFweU1-WkR' 
```
4) 
```
curl --location 'http://localhost:8080/api/v1/convert' \
--header 'Authorization: Bearer e8EA-8DLq-9Pz6P_mMBHs3w4VHEU89Q_MrFweU1-WkR' \
--header 'Content-Type: application/json' \
--data '{
    "currency_from": "BTC",
    "currency_to": "USD",
    "value": 1000
}'
```

токен тут - https://github.com/senatorovNi/perfect-panel/blob/main/.env#L68

Релизация:
1) проверку токена сделал через middleware - https://github.com/senatorovNi/perfect-panel/blob/main/app/Http/Middleware/CheckToken.php
2) всю логику вынес в сервис для работы с апи - https://github.com/senatorovNi/perfect-panel/blob/main/app/Services/CurrencyExchangeService.php


Под валюты не стал создавать entity, так как подумал, что это избыточно для двух методов

