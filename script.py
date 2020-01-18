# using SendGrid's Python Library
# https://github.com/sendgrid/sendgrid-python

import os
import time
import requests
from sendgrid import SendGridAPIClient
from sendgrid.helpers.mail import Mail
import config

while True:
    print('Starting parsehub job...')

    url = 'https://www.parsehub.com/api/v2/projects/' + config.PARSEHUB_PROJECT_TOKEN + '/run'
    data = {'api_key': config.PARSEHUB_API_KEY}
    r = requests.post(url, data=data)

    data = r.json()
    run_token = data['run_token']

    time.sleep(3)

    wait = True
    while wait:
        url = 'https://www.parsehub.com/api/v2/runs/' + run_token + '/data?api_key=' + config.PARSEHUB_API_KEY + '&format=json'
        r2 = requests.get(url)
        try:
            try_json_or_404 = r2.json()
            data = r2.text
            wait = False
        except:
            time.sleep(3)
            print('Waiting for parsehub...')

    message = Mail(
        from_email = config.EMAIL,
        to_emails = config.EMAIL,
        subject = 'Python script.py API alert',
        html_content = 'Result: <strong>' + data + '</strong>')
    try:
        sg = SendGridAPIClient(config.SENDGRID_API_KEY)
        response = sg.send(message)
        if (response.status_code == 202):
            print('Email sent at ' + time.strftime('%Y-%m-%d %H:%M:%S'))
            
        """ print(response.status_code)
        print(response.body)
        print(response.headers) """
    except Exception as e:
        print(e.message)

    time.sleep(24*60*60) # sleep 1 day
