# Parsehub API alert

Short API alert script sends an email every day with the crawled data (crawling done by Parsehub). Anyway, if you're not a fan of Parsehub, you can use this code, just remove the unnecessary stuff.

This script starts the Parsehub job, check if it finished (if not, wait 3 sec and recheck). After that, it sends an email to the configured email address with Sendgrid. **You need a Parsehub and a Sendgrid account (free) with an API key.** Copy `config_default.py` to `config.py` and put the api keys into it.