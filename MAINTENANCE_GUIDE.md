# Graceful Service Degradation During Maintenance

When the application is in maintenance mode, it's important to ensure that services degrade gracefully. This means that users are not left with a broken experience and that long-running jobs are not interrupted.

## Long-Running Jobs

If your application has long-running jobs, it's important to ensure that they are not interrupted when maintenance mode is enabled. One way to do this is to use a queueing system that supports pausing and resuming jobs. When maintenance mode is enabled, you can pause the queue and resume it when maintenance is complete.

## User Notifications

It's important to notify users of upcoming maintenance. You can do this by displaying a banner on the application's website or by sending an email to all users. You should also provide an estimated downtime so that users know when to expect the application to be back online.

## API Endpoints

When the application is in maintenance mode, all API endpoints should return a 503 Service Unavailable status code. This will prevent clients from receiving unexpected errors and will allow them to handle the maintenance mode gracefully.