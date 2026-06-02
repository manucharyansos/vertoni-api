# Newsletter mail flow

This project sends two types of newsletter emails:

1. Welcome email after a new subscription is created or an inactive subscription is reactivated.
2. New product email when an admin creates an active product and leaves "Նոր ապրանքի մասին նամակ ուղարկել բաժանորդներին" enabled.

The admin can also open a product edit page and click "Ուղարկել newsletter" to send that product manually.

Emails are dispatched after the HTTP response, so admin forms and the public subscription request are not blocked by SMTP work. If `QUEUE_CONNECTION` is configured as a real queue and a worker is running, the jobs can be processed by the queue. If `QUEUE_CONNECTION=sync`, they still run after the response.

Required production env keys:

```env
NEWSLETTER_MAIL_ENABLED=true
NEWSLETTER_WELCOME_EMAIL_ENABLED=true
NEWSLETTER_NEW_PRODUCT_EMAIL_ENABLED=true
NEWSLETTER_EMAIL_BATCH_SIZE=50
MAIL_MAILER=smtp
MAIL_HOST=mail.verthoni.com
MAIL_PORT=465
MAIL_USERNAME=info@verthoni.com
MAIL_PASSWORD=PUT_REAL_PASSWORD
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@verthoni.com
MAIL_FROM_NAME="Verthoni"
```

If mail is slow or the subscriber list becomes large, switch to a real queue:

```env
QUEUE_CONNECTION=database
```

Then run a worker through hosting cron or a process manager:

```bash
php artisan queue:work --tries=2 --timeout=300
```
