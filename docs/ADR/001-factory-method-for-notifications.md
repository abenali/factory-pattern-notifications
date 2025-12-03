# ADR 001: Utilisation du Factory Method Pattern pour le système de notifications

## Statut
✅ Accepté - [Date]

## Contexte

### Problème métier

[Réponds à ces questions :]
- Quel est le problème business ? (envoyer des notifications via plusieurs canaux)
- Pourquoi c'est complexe ? (chaque canal a des dépendances différentes)
- Quelles sont les contraintes métier ? (préférences user, disponibilité des canaux)

Le système NotifyHub doit permettre d'envoyer des notifications aux utilisateurs via le canal le plus pertinent disponible pour eux (Email, SMS, Push, Slack).
La complexité vient du fait que chaque canal de notification requiert une configuration, un client API, et des dépendances qui lui sont propres (ex: Mailer pour l'email, un fournisseur externe pour les SMS).
Les contraintes business sont de respecter les préférences de l'utilisateur tout en ayant une logique de fallback si le canal préféré n'est pas disponible (ex: email non vérifié, pas de numéro de téléphone).

### Problèmes techniques

[Réponds :]
- Si on code naïvement avec des `if/else` et des `new`, quels principes SOLID sont violés ?
- Quel est le problème de couplage ?
- Quelle est la difficulté à tester ?

D'un point de vue technique, une approche naïve avec des structures conditionnelles (`if/else` ou `switch`) dans un service mène à :
- Violation du **Single Responsibility Principle (SRP)** car le service se charge à la fois de la logique de sélection du canal et de la construction de chaque notificateur.
- Violation de l'**Open/Closed Principle (OCP)** car l'ajout d'un nouveau canal nécessite de modifier ce service.
- **Couplage fort** car le service central (ex: `NotificationService`) connaîtrait directement toutes les classes de notificateurs concrets et leurs dépendances.
- **Difficulté à tester** car pour tester l'envoi d'un email, il faudrait potentiellement mocker les dépendances de tous les autres canaux (SMS, Push, etc.).

---

## Décision

Nous avons décidé d'utiliser le **Factory Method Pattern** pour encapsuler la création des notifiers.

### Pourquoi Factory Method (et pas Simple Factory) ?

[Explique :]
- Quelle est la différence entre Simple Factory et Factory Method ?
- Pourquoi Factory Method est mieux ici ?
- Comment ça permet l'extensibilité ?

Le Factory Method Pattern résout notre problème en déléguant la responsabilité de l'instanciation à des sous-classes (les factories concrètes).

Différence avec Simple Factory :
- **Simple Factory** est souvent une seule classe avec une grande méthode `create` qui contient un `switch` pour instancier le bon objet. Elle centralise la création mais viole l'OCP.
- **Factory Method** définit une interface (ou une classe abstraite) pour créer un objet, mais laisse les sous-classes décider quelle classe concrète instancier.

Nous choisissons une approche basée sur le Factory Method (via un Registry de factories) car :
1.  **Extensibilité (OCP)** : Ajouter un nouveau canal (ex: WhatsApp) se fait en créant une nouvelle classe `WhatsAppNotifierFactory`. Le code existant (le registry, le service client) n'est pas modifié.
2.  **Séparation des responsabilités (SRP)** : Chaque factory (`EmailNotifierFactory`, `SmsNotifierFactory`) a une seule responsabilité : savoir comment construire UN type de notificateur et gérer SES propres dépendances.
3.  **Testabilité** : On peut tester chaque factory de manière isolée, ainsi que la logique de notification sans dépendre de toutes les implémentations concrètes.

---

## Conséquences

### Positives ✅

[Liste au moins 5 bénéfices concrets :]

1. **Respect de SOLID** :
    - Single Responsibility : [Explique pour NotificationService, ChannelSelector, Factories]
    - Open/Closed : [Explique comment ajouter un canal]
    - Dependency Inversion : [Explique les interfaces]

2. **Découplage** : [Explique ce que NotificationService ne connaît plus]

3. **Testabilité** : [Explique comment mocker les factories]

4. **Extensibilité** : [Temps pour ajouter un nouveau canal]

5. **Maintenabilité** : [Code organisé, facile à comprendre]

1.  **Respect de SOLID** :
    - **SRP** : `NotificationService` orchestre, `ChannelSelector` choisit, et chaque `XxxFactory` construit. Les responsabilités sont clairement délimitées.
    - **OCP** : Le système est ouvert à l'extension (nouveaux canaux) sans modifier le code existant.
    - **DIP** : Le code applicatif dépend d'abstractions (`NotifierInterface`, `NotifierFactoryInterface`) et non d'implémentations.

2.  **Découplage** : `NotificationService` ne connaît plus les notificateurs concrets (`EmailNotifier`, `SmsNotifier`...). Il ne manipule que l'interface `NotifierInterface`.

3.  **Testabilité** : Pour tester le service principal, il suffit de mocker le `NotifierFactoryRegistry` pour qu'il retourne un faux notificateur. Chaque canal peut être testé indépendamment.

4.  **Extensibilité** : L'ajout d'un nouveau canal est rapide et à faible risque, car il se limite à l'ajout de nouvelles classes sans impacter l'existant.

5.  **Maintenabilité** : Le code est mieux organisé, plus lisible et plus facile à comprendre pour les nouveaux développeurs. La logique de chaque canal est isolée.

### Négatives ⚠️

[Sois honnête :]

1. **Nombre de classes** : [Combien de classes vs approche naïve ?]

2. **Complexité initiale** : [Temps de setup vs approche simple]

3. **Over-engineering** : [Dans quels cas ce serait trop ?]

1.  **Nombre de classes** : Pour 4 canaux, cette approche nécessite environ 10 classes/interfaces (interfaces, factories, notifiers, registry) contre 5 dans une approche naïve.

2.  **Complexité initiale** : Le temps de mise en place de la structure (interfaces, registry, injection de dépendances tagguées) est plus élevé qu'un simple `switch`.

3.  **Over-engineering** : Si le projet n'avait eu que 2 canaux fixes sans perspective d'évolution, cette solution aurait pu être considérée comme surdimensionnée.

---

## Alternatives considérées

### Alternative 1 : Simple Factory (une seule classe)

**Description :**
```php
class NotifierFactory
{
    public function create(NotificationChannel $channel): NotifierInterface
    {
        return match ($channel) {
            NotificationChannel::EMAIL => new EmailNotifier($this->mailer),
            NotificationChannel::SMS => new SmsNotifier($this->smsProvider),
            // ...
        };
    }
}
```

**Pourquoi rejetée :**

[Explique :]
- Quel principe SOLID est violé ?
- Quel est le problème de couplage ?
- Que se passe-t-il quand on ajoute 10 canaux ?

Cette approche viole l'**Open/Closed Principle**. Chaque nouvel ajout de canal force la modification de la classe `NotifierFactory`.
Le problème de couplage est que la factory doit connaître toutes les dépendances de tous les notificateurs, devenant ainsi une classe "fourre-tout".
Avec 10 canaux, la classe devient difficile à maintenir et à tester, et les fusions de code sur ce fichier deviennent problématiques.

### Alternative 2 : Service Locator

**Description :**
```php
class NotifierLocator
{
    public function __construct(private ContainerInterface $container) {}
    
    public function get(string $channelName): NotifierInterface
    {
        return $this->container->get("notifier.{$channelName}");
    }
}
```

**Pourquoi rejetée :**

[Explique :]
- Qu'est-ce qu'un Service Locator ?
- Pourquoi c'est considéré comme un anti-pattern ?
- Quel est le problème pour les tests ?

Le Service Locator est un pattern qui permet de récupérer des services depuis un conteneur central.
C'est souvent considéré comme un anti-pattern car il **masque les dépendances** d'une classe. En lisant le constructeur, on ne voit pas que la classe dépend indirectement de 10 autres services.
Pour les tests, le problème est qu'il faut fournir un conteneur de services complet et configuré, ce qui rend les tests unitaires plus complexes et moins isolés.

### Alternative 3 : Dependency Injection directe de tous les notifiers

**Description :**
```php
class NotificationService
{
    public function __construct(
        private EmailNotifier $emailNotifier,
        private SmsNotifier $smsNotifier,
        private PushNotifier $pushNotifier,
        private SlackNotifier $slackNotifier
    ) {}
}
```

**Pourquoi rejetée :**

[Explique :]
- Quel est le problème de cette approche ?
- Que se passe-t-il avec 20 canaux ?

Cette approche crée un "constructor over-injection". Le constructeur du service devient énorme et difficile à utiliser.
Avec 20 canaux, le constructeur aurait 20 arguments, ce qui est un signe de mauvaise conception et rend la classe quasi impossible à instancier manuellement en test.

---

## Implémentation technique

### Architecture choisie

Nous structurons le code selon **Clean Architecture** :

**Domain** :
- Interface `NotifierInterface` : contrat des notifiers (`send`)
- Interface `NotifierFactoryInterface` : contrat des factories (`create`, `supports`)
- Entités : `User`, `Notification`
- Value Objects / Enums : `NotificationChannel` (enum), `UserId`, `EmailAddress`

**Application** :
- Use Case / Handler : `SendNotificationHandler`
- Service : `ChannelSelector` (détermine le meilleur canal)
- DTOs / Commands : `SendNotificationCommand`

**Infrastructure** :
- Factories concrètes : `EmailNotifierFactory`, `SmsNotifierFactory`, etc. (implémentent `NotifierFactoryInterface`)
- Registry : `NotifierFactoryRegistry` (trouve la bonne factory via les services taggués)
- Notifiers concrets : `EmailNotifier`, `SmsNotifier`, etc. (implémentent `NotifierInterface`)
- Adapters : Clients HTTP pour les API externes (SMS, Push, Slack).

**Presentation** :
- Controller API REST (`NotificationController`)
- Command CLI (`notification:send`)

### Flux d'exécution

[Décris le flux complet étape par étape]

1.  Un `Controller` reçoit la requête HTTP et la transforme en `SendNotificationCommand`.
2.  Le Controller appelle directement le `SendNotificationHandler`.
3.  Le `SendNotificationHandler` :
    a. Récupère l'entité `User` via un `UserRepository`.
    b. Appelle le service `ChannelSelector` avec l'utilisateur pour déterminer le `NotificationChannel` à utiliser.
    c. Demande au `NotifierFactoryRegistry` la factory appropriée pour ce canal.
    d. Utilise la factory obtenue pour créer une instance du `NotifierInterface`.
    e. Appelle la méthode `send()` du notificateur avec le message.
    f. Persiste une entité `Notification` pour l'historique.
    g. Retourne une réponse.
4.  Le `Controller` transforme la réponse en JSON et retourne une réponse HTTP 202 Accepted.

### ChannelSelector : Logique de sélection

[Explique l'algorithme de sélection du canal]

Le `ChannelSelector` détermine le canal avec la logique suivante :
1.  Vérifie le canal préféré défini par l'utilisateur dans ses préférences.
2.  Si ce canal est disponible (ex: `user.isEmailVerified()` pour le canal EMAIL), il est retourné.
3.  Sinon, il applique un ordre de priorité de fallback :
    - Email (si `user.isEmailVerified()`)
    - Push (si `user.hasPushToken()`)
    - SMS (si `user.hasPhoneNumber()`)
    - Slack (si `user.isSlackConnected()`)
4.  Si aucun canal n'est disponible après toutes ces vérifications, une exception `NoAvailableChannelException` est levée.

### Gestion des dépendances des notifiers

[Explique comment chaque factory gère ses propres dépendances]

Chaque factory encapsule la connaissance de ses dépendances, qui lui sont fournies par le conteneur de DI de Symfony.

**EmailNotifierFactory** :
- Dépend de : `Symfony\Component\Mailer\MailerInterface`
- Crée : `new EmailNotifier($this->mailer)`

**SmsNotifierFactory** :
- Dépend de : `App\Infrastructure\Sms\SmsProviderInterface`, `string $smsApiKey` (paramètre injecté)
- Crée : `new SmsNotifier($this->smsProvider, $this->smsApiKey)`

**PushNotifierFactory** :
- Dépend de : `Psr\Http\Client\ClientInterface`, `string $pushApiEndpoint`
- Crée : `new PushNotifier($this->httpClient, $this->pushApiEndpoint)`

**SlackNotifierFactory** :
- Dépend de : `App\Infrastructure\Slack\SlackClient`
- Crée : `new SlackNotifier($this->slackClient)`

### Registry Pattern

[Explique comment le Registry trouve la bonne factory]

Le `NotifierFactoryRegistry` est une classe qui centralise l'accès à toutes les factories.
- Son constructeur reçoit un `iterable` de `NotifierFactoryInterface` grâce au mécanisme de **services taggués** de Symfony. Toutes les factories sont tagguées avec `app.notifier_factory`.
- Il possède une méthode `getFactory(NotificationChannel $channel)` qui itère sur toutes les factories injectées.
- Pour chaque factory, il appelle sa méthode `supports($channel)`.
- Il retourne la première factory qui répond `true`, ou lève une `UnsupportedChannelException` si aucune n'est trouvée.

---

## Impact sur les tests

[Explique comment Factory améliore la testabilité vs code naïf]

Avant (code naïf avec if/else + new) :
- Le `NotificationService` dépendait de tous les clients (Mailer, SMS, Push...).
- Pour tester un seul canal, il fallait mocker toutes les dépendances, même celles inutilisées dans le cas de test.
- Les tests étaient lourds et fragiles.

Après (Factory Method) :
- Chaque factory est testée unitairement pour vérifier qu'elle crée le bon notificateur avec les bonnes dépendances.
- Le `SendNotificationHandler` est testé en mockant le `NotifierFactoryRegistry` pour lui faire retourner un `FakeNotifier`.
- Les tests sont rapides, ciblés et robustes. L'ajout d'un canal ne casse pas les tests existants.

---

## Extensibilité

**Pour ajouter un nouveau canal (ex: WhatsApp) :**

[Liste les étapes concrètes]

1.  Créer la classe `WhatsAppNotifier` qui implémente `NotifierInterface`.
2.  Créer la classe `WhatsAppNotifierFactory` qui implémente `NotifierFactoryInterface`.
3.  Dans `services.yaml`, ajouter le tag `app.notifier_factory` au service `WhatsAppNotifierFactory`.
4.  Ajouter la valeur `WHATSAPP` à l'enum `NotificationChannel`.
5.  Mettre à jour la logique du `ChannelSelector` si nécessaire.
6.  Créer les tests unitaires pour `WhatsAppNotifier` et `WhatsAppNotifierFactory`.

Aucune modification du code existant (`SendNotificationHandler`, `NotifierFactoryRegistry`) n'est nécessaire.
Temps estimé : environ 20-30 minutes.

---

## Métriques de décision

| Métrique | Code naïf (if/else + new) | Factory Method | Impact |
|----------|----------------------------|----------------|--------|
| Nombre de classes | 1 | 10+ | + organisation |
| Dépendances NotificationService | 10+ | 1 (registry) | + découplage |
| Lignes de code par classe | ~150 | ~20-30 | + lisibilité |
| Temps ajout d'un canal | ~45 min | ~15 min | + productivité |
| Testabilité (1-10) | 3/10 | 9/10 | + qualité |

---

## Notes d'implémentation

- Pattern reconnu de l'industrie (GoF)
- Compatible avec Symfony Dependency Injection (tagged services)
- Peut évoluer vers Abstract Factory si besoin de familles de notifiers
- Le Registry utilise le pattern Iterator sur les factories

---

## Références

- [Design Patterns - GoF](https://en.wikipedia.org/wiki/Design_Patterns)
- [Refactoring Guru - Factory Method](https://refactoring.guru/design-patterns/factory-method)
- [Symfony Service Tags](https://symfony.com/doc/current/service_container/tags.html)
