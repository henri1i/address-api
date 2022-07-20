# Addresses Crud

## API Endpoints

<br>

### **Authentication**

|          | Endpoint              | Payload                                      | Verb |
|----------|-----------------------|----------------------------------------------|------|
| Register | .../api/auth/register | name, email, password, password_confirmation | POST |
| Login    | .../api/auth/login    | email, password                              | POST |
| Logout   | .../api/auth/logout   | token                                        | POST |
| Refresh  | .../api/auth/refresh  | token                                        | POST |


<br>

### **Addresses**


|         | Endpoint                                                                                                                              | Payload                                   | Verb   |   |   |   |   |   |   |
|---------|---------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------|--------|---|---|---|---|---|---|
| Index   | .../api/addresses                                                                                                                     | token, (per_page)                         | GET    |   |   |   |   |   |   |
| Show    | .../api/addresses/{id}                                                                                                                | token                                     | GET     |   |   |   |   |   |   |
| Store   | .../api/addresses                                                                                                                     | token, cep, house_number, reference_point | POST   |   |   |   |   |   |   |
| Update  | .../api/addresses/{id}                                                                                                                | token, (fields to be updated)             | PUT    |   |   |   |   |   |   |
| Destroy | .../api/addresses/{id}                                                                                                                | token                                     | DELETE |   |   |   |   |   |   |

## Downloading and setting up the project

(Are you in a hurry? Run the installation script with: `./install.sh`)

1. Clone project:

``` bash
$ git clone https://github.com/henri1i/address-api.git
$ cd address-api
```

2. Install dependencies:

``` bash
$ composer install
```

3. Setting up env

``` bash
$ cp .env.example .env
```

4. Start the application

``` bash
$ sail up -d
```

5. Generate env variables
``` bash
$ php artisan key:generate
$ php artisan jwt:secret
```

5. Run the migrations

``` bash
$ sail php artisan migrate
$ sail php artisan db:seed #Just if you want to run the seeders
```

Enjoy!

http://localhost:80/
