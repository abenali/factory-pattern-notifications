# 🚀 Guide de Setup

## Utilisation de ce template

Ce template est conçu pour démarrer rapidement un nouveau projet d'apprentissage des Design Patterns avec Symfony.

### 1. Créer un nouveau projet depuis ce template

Sur GitHub, cliquez sur "Use this template" → "Create a new repository"

Ou en ligne de commande :
```bash
git clone [url-du-nouveau-repo]
cd [nouveau-repo]
```

### 2. Personnaliser le projet

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Éditer .env et changer PROJECT_NAME
# Exemple : PROJECT_NAME=strategy_pattern
```

### 3. Démarrer l'environnement

```bash
# Build et démarrage
docker-compose up -d --build

# Installer les dépendances
docker-compose exec php composer install

# Créer la base de données
docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:migrations:migrate -n
```

### 4. Vérifier l'installation

```bash
# Tester la qualité du code
make quality

# L'application devrait être accessible sur :
# http://localhost:8080
```

### 5. Structure à compléter

À chaque nouveau pattern, complétez :

- [ ] `README.md` avec le nom du pattern et le use case
- [ ] `docs/PATTERN.md` avec l'explication théorique
- [ ] `docs/USE_CASE.md` avec les spécifications métier
- [ ] `docs/ADR/001-*.md` avec vos décisions d'architecture
- [ ] Le code dans `src/` en suivant Clean Architecture
- [ ] Les tests dans `tests/`

### 6. Checklist avant de commiter

- [ ] `make quality` passe sans erreur
- [ ] Les tests sont verts
- [ ] Le README est à jour
- [ ] Les docs sont complètes
- [ ] Les ADR sont rédigés

## Commandes utiles

```bash
# Tests
make test                 # Lancer les tests
php bin/phpunit --coverage-html var/coverage  # Avec couverture

# Qualité
make phpstan             # Analyse statique
make cs-fix              # Formater le code
make quality             # Tout en un

# Base de données
docker-compose exec php bin/console doctrine:migrations:migrate
docker-compose exec php bin/console doctrine:fixtures:load

# Logs
docker-compose logs -f php
```

## Troubleshooting

### Port déjà utilisé
Si le port 8080 est déjà pris, changez `NGINX_PORT` dans `.env`

### Permission denied
```bash
sudo chown -R $USER:$USER .
```

### Reconstruire les containers
```bash
docker-compose down -v
docker-compose up -d --build
```
