.DEFAULT_GOAL = help
.PHONY = help test

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

start: ## Start containers
	docker compose up -d

stop: ## Stop containers
	docker compose stop

test: ## Run Test
	docker compose exec -t php composer test

sh: ## Open a shell in the container
	docker compose exec -it php sh
