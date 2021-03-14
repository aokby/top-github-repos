#GitHub Top Repositories API

Get most popular GitHub repositories and filter it by creation date and/or programming language

#Environment (using Docker)
- php 7.4
- NGINX 1.19
- Symfony skeleton 4.4

#Installation
- `docker-compose build`
- `docker-compose up`
- Visit http://localhost:8080/api/doc for API Documentation UI
- Or Access the API directly http://localhost:8080/top_repositories
- For unit test run `docker-compose exec php bin/phpunit`
