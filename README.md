# 🎯 [Nom du Pattern] - [Nom du Use Case]

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)
[![Symfony Version](https://img.shields.io/badge/symfony-7.3-black)](https://symfony.com/)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%206-brightgreen)](https://phpstan.org/)
[![Tests](https://img.shields.io/badge/tests-passing-success)](phpunit.xml.dist)

## 📖 Contexte

**Design Pattern étudié :** [Nom du Pattern]  
**Domaine métier :** [E-commerce / SaaS / Finance / etc.]  
**Problème résolu :** [Décrire en 1-2 phrases le problème métier]

## 🎓 Objectifs pédagogiques

- Comprendre le pattern [X] et ses cas d'usage
- Appliquer les principes SOLID (notamment [principe pertinent])
- Structurer une application en Clean Architecture
- Écrire des tests unitaires robustes

## 🏗️ Architecture

Ce projet suit les principes de **Clean Architecture** avec une séparation stricte des couches :

```
src/
├── Domain/          # Logique métier pure (aucune dépendance externe)
├── Application/     # Cas d'usage (orchestration)
├── Infrastructure/  # Implémentations techniques (DB, API, etc.)
└── Presentation/    # Controllers & API
```

### Décisions d'architecture

- [ADR-001](docs/ADR/001-why-this-pattern.md) : Pourquoi ce pattern ici ?
- Voir le dossier `docs/ADR/` pour toutes les décisions

## 🚀 Installation

```bash
# Cloner le projet
git clone [url-du-repo]
cd [nom-du-repo]

# Démarrer Docker
docker-compose up -d

# Installer les dépendances
docker-compose exec php composer install

# Créer la base de données
docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:migrations:migrate -n

# Charger les fixtures (optionnel)
docker-compose exec php bin/console doctrine:fixtures:load -n
```

## 🧪 Tests

```bash
# Lancer tous les tests
make test

# Tests unitaires uniquement
php bin/phpunit --testsuite=Unit

# Tests avec couverture
php bin/phpunit --coverage-html var/coverage
```

## 🔍 Qualité de code

```bash
# Analyse statique
make phpstan

# Formatage du code
make cs-fix

# Tout en une fois
make quality
```

## 📚 Documentation

- [Explication du Pattern](docs/PATTERN.md) : Théorie et concepts
- [Use Case métier](docs/USE_CASE.md) : Spécifications détaillées
- [Diagrammes UML](docs/diagrams/) : Architecture visuelle

## 🎯 Points clés de l'implémentation

### Le pattern en action

```php
// Exemple de code montrant le pattern
```

### Respect des principes SOLID

- **S** - Single Responsibility : [Explication]
- **O** - Open/Closed : [Explication]
- **L** - Liskov Substitution : [Explication]
- **I** - Interface Segregation : [Explication]
- **D** - Dependency Inversion : [Explication]

## 🤔 Réflexions et apprentissages

### Ce qui a bien fonctionné

- [Point 1]
- [Point 2]

### Difficultés rencontrées

- [Difficulté 1] → Solution : [...]
- [Difficulté 2] → Solution : [...]

### Pour aller plus loin

- [Amélioration possible 1]
- [Amélioration possible 2]

## 📝 Ressources

- [Documentation officielle du pattern](lien)
- [Article de référence](lien)
- [Autres ressources pertinentes](lien)

## 👤 Auteur

**[Ton Nom]**  
En formation continue sur les Design Patterns avec Symfony

*Projet réalisé dans le cadre d'un parcours d'apprentissage des Design Patterns*

---

⭐ Si ce projet vous a été utile, n'hésitez pas à lui donner une étoile !
