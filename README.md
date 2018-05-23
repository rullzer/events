# Events

This app will allow you to manage events. You can share those events with friends
and family so they can upload all the pictures and videos taken during that event
easily.


## API


### Create a new event

A post to:

`<server>/apps/events/api/v1`

With

`eventName=<NAME>&start=<START>&end=<END>`

where:

* NAME = the name of the event
* START = the start as unix timestamp in GMT
* END = the end as unix timestamp in GMT

This returns you the event token

### Get the event landing page

Go to `<server>/apps/events/<TOKEN>`

### Get the general event info

A get to:

`<server>/apps/events/api/v1/<TOKEN>`

Will get you the event info as well as the READ ONLY token (for the general public
webdav endpoint) of the event.

The url is the url to use as webdav endpoint. Use the token as your username.

### Get write access to the event

A post to:

`<server>/apps/events/api/v1/<TOKEN>`

with

`username=<USERNAME>`

where:
* USERNAME = your username

will create a write token to upload your files to.

The url is the url to use as webdav endpoint. Use the token as your username.
