# Duino Coin - PHP Miner
This is a proof of concept.
This miner is provided as is, with no guarantee it will work as intended for you.
I don't intend to actively maintain this miner.
I'm also not responsible for how you use it.

## Why did you do it?
I only made this to have some fun and to prove that [PHP 8 is indeed faster than python](https://scand.com/company/blog/php-vs-python/#:~:text=Some%20years%20ago%2C%20Python%20was,speed%20may%20greatly%20improve%20performance.).

# Requirements
Either Docker (v20) or PHP 8 & Composer installed locally.

# How to use
First, add your username into the configuration section in the `miner.php` file.
  
### If you use docker
Run composer install and then run the miner:
```bash
$ docker run --rm --interactive --tty \
  --volume $PWD:/app \
  composer install
  
$ docker compose up
```

### If you have PHP 8 installed
Install dependencies with composer:
`$ composer install`  
Run the miner:
`$ php miner.php`

# Multi-thread improvements
If you have a good computer and want to improve the performance you can run multiple workers/miners at once.
For this you have to be using docker.

Simply run:  
`$ docker compose up --scale miner=32`  
Where the number "32" is the amount of workers you will be running.

> Keep in mind that the maximum amount of allowed workers is 50.
> I'm not responsible for any bad usage of this that might get you banned from the Duino pool.

# Future improvements
There are MANY possibilities of improvements to have a higher hashrate, however, due to the nature of DuinoCoin and its mission to allow everyone to mine, I don't intend to seek such improvements in this lib.
