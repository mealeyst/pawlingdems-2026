.PHONY: install build

all:
	@echo "Usage: make <install|build|clean>"

install:
	@echo "Installing requirements for ionos-assistant ..."
	npm install
	composer install --ignore-platform-reqs
	npm run compileSassInFeatureBlueprint

clean:
	@echo "Cleaning requirements for ionos-assistant ..."
	rm -rf vendor
	rm -rf node_modules

build:
	@echo "TODO: Building package ..."