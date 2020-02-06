# Price drop alert

__Compatible with ANY WEBSITE!__

First, you need to extract the price data from your webpage/webshop. Do it with Parsehub, which is free!

After you created your API, this script can read this.

Example Parsehub API:
```
{
 "product_1": "$149",
 "product_2": "690 EUR/pc",
 "product_3": "79900 USD"
}
```

Example config.php:
```
$ALERTS = array(
    ['product_1', 100],
    ['product_2', 500],
    ['product_3', 60000],
);
```

So you will get an email when _product_1_ price drops below 100, or product_2 < 500, or product_3 < 60000...

**You need a Parsehub and a Sendgrid account (free) with an API key.** Copy `config_default.php` to `config.php` and put the api keys into it.

Recommended to use [cron-job.org](https://cron-job.org/en/) if you want to run the script periodically.
