
## Installing
To run the application just follow the steps below.

Create .env file from .env.exmaple.
```
$ cp .env.example .env
```
We basically need two databases, the extra one is for testing. So make sure you change the **DB_DATABASE** from **TESTING_DB_DATABASE** columns.

Run composer
```
$ composer install
```
Create the .env file
```
$ cp .env.example .env
```

Create tables
```
$ php artisan migrate
```

## Start the server
```
$ php artisan serve
$ npm run dev
```

## Usage
Register and Login:

Create an account or log in if you already have one.

Play the Game:

The game starts with you and 9 other players. You can vote by clicking Kick button next to the player you want to kick.
During the day phase each player is going to vote and the player with the most votes will be kicked out. During the night mafias will choose whom to kick. If you are assigned with a Mafia role you can directly kick the other player no votes will be counted.

Winning the Game:

Villagers win if they successfully eliminate all Mafia members.
Mafia wins if they outnumber the Villagers.

Features
User authentication
Simplified two-role gameplay with Villagers and Mafia.
Web-based interface for easy access and interaction.
