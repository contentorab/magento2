## Working with the code

This repository uses Docker and [docker-compose](https://docs.docker.com/compose/)
to manage its development environment.

When running the Docker images the Magento2 instance is made available on port
7000.

There are a few scripts available to manage this environment under `dev/`.

* `dev/start.sh` - start the Docker containers
* `dev/setup.sh` - perform initial setup of database and example data
* `dev/refresh.sh` - refresh links to the module
* `dev/shell.sh` - open a shell on the Magento container
* `dev/build.sh` - build and package the extension
* `dev/destroy.sh` - destroy the containers and all their data

### Initial setup

Start the containers:

```
$ dev/start.sh
```

Setup database and example data:

```
$ dev/setup.sh
```

### Using the admin interface

The admin interface should be available at `http://localhost:7000/admin`. Use
`admin` with the password `magentoadmin1` to sign in.

### When deploying

Remember that you need to keep track of the parameter for the API URL, to ensure beta does not go live:

File:
* `/etc/config.xml`

Setting:
* Prod:  `api.contentor.com` 
* Stage: `beta.contentor.com`
