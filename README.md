# Vote System

![Automated tests](https://github.com/WesleyKlop/vote-system/workflows/Automated%20tests/badge.svg?event=push)
![Deliver](https://github.com/WesleyKlop/vote-system/workflows/Deliver/badge.svg)

![Question example](.github/screenshots/banner.png)

Vote System is a general purpose voting application that,
especially during these stay-at-home times can be useful in enabling digital voting.

## Usage / Deployment

This fork ships as scaffolding for a self-hosted deployment, not a managed service - you're
responsible for running and securing your own instance. Docker Compose here is meant for a single
host; for real production scale, use Swarm, K8s, or similar.

### Deployment with docker-compose (recommended)

Download just [docker-compose.yml](./docker-compose.yml) - no `.env` file needed - and run:

```bash
docker compose up -d
```

That's it. On first boot this initializes the database, generates an application key, an admin
password and a websockets secret, runs the migrations, and starts the application. The generated
admin password is printed once to the container log (`docker compose logs vote-system`) and then
persisted, so restarts keep working without you having to save it manually. The app is reachable at
[localhost:8080](http://localhost:8080).

To customize anything (admin credentials, ports, `APP_URL` for a real domain, etc.), drop a `.env`
file next to `docker-compose.yml` - Compose picks it up automatically - or export the same variables
in your shell before running `docker compose up`. See [.env.example](./.env.example) for the full
list of what's configurable; anything you don't set falls back to a working default.

### Manual docker deployment

The easiest way to use and deploy this application is using Docker.
You can grab the latest version from this GitHub or use a certain tag by viewing the [ghcr versions page](https://github.com/users/WesleyKlop/packages/container/vote-system/versions).

Provide config as `-e` flags matching [.env.example](./.env.example), or bind-mount a real `.env`
file the same way as before:

```bash
ENV_FILE=/abs/path/to/your/.env-file
IMAGE=ghcr.io/wesleyklop/vote-system:main
WEB_PORT=8080 # Make sure this matches the port in APP_URL
docker run --rm -d -p 6001:6001 -v $ENV_FILE:/app/.env $IMAGE php artisan websockets:serve
docker run --rm -d -p $WEB_PORT:80 -v $ENV_FILE:/app/.env $IMAGE
```

You'll also need a Postgres (recommended) or MySQL database reachable from the container - the
docker-compose path provisions one for you automatically.

The docker-compose path above is simpler and recommended unless you have a specific reason not to
use it.

## Screenshots

![Admin dashboard](.github/screenshots/dashboard.png)

## Contributing

Contributing guidelines can be found in [CONTRIBUTING.md](./CONTRIBUTING.md)

## Security Vulnerabilities

If you discover a security vulnerability within Vote system, please send an e-mail to Wesley Klop via [wesley19097@gmail.com](mailto:wesley19097@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The Vote system is open-sourced software licensed under the [GPLv3](https://opensource.org/licenses/GPL-3.0).
