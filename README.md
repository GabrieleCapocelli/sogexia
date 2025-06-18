
# 🧾 Sogexia - Test Technique

<h2 id="init">INIT</h2>


##### démarrer l'application
```
docker compose up -d --build
```

##### accéder au container
```
docker compose exec -it api /bin/bash
```

#### créer la base de données, mettre à jour le schema et charger les fixtures
```
bin/console d:d:c
bin/console d:s:u -f --complete
echo yes | bin/console d:f:l
```

#### sortir du container
```
exit
```

#### pour utiliser le client:
```
http://localhost:8080
```

#### fermer l’application et retirer les conteneurs
```
docker compose stop
docker compose rm
```

## Structure de l'API

### └── `Controller/`
Contient les points d'entrée de l'API REST.

- `ProductIndexController.php` — expose l’endpoint `GET /api/v1/products`
- `ProductUpdateController.php` — gère la mise à jour `PUT /api/v1/products/{id}`

---

### └── `DataFixtures/`
- `AppFixtures.php` — charge des données initiales (`Product`) pour développement et test.

---

### └── `Dto/`
Définit la structure des données attendues côté API.

- `ProductDto.php` — représente les champs d'entrée JSON pour un produit.
- `StockDto.php` — encapsule les informations de stock imbriquées (`available`, `description`).

---

### └── `Entity/`

- `Product.php` — entité avec mapping Doctrine.

---

### └── `Enum/`
- `Status.php` — Enum pour le champ `status` d’un produit.

---

### └── `EventListener/`

- `ExceptionListener.php` — convertit automatiquement les exceptions (405, 500) en réponses JSON formatées.

---

### └── `Repository/`

- `ProductRepository.php` — repository custom pour `Product`.

---

### └── `Service/`

- `ApiErrorService.php` — génère les réponses d'erreur standardisées selon les codes d'erreur définis.
- `ApiSecurityService.php` — vérifie les en-têtes HMAC, les signatures entrantes et construit les signatures sortantes.

---

## 🧭 Structure du Client


### └── `public/`

- `index.php` — page de visualisation ou navigation principale
- `put.php` — soumet une requête `PUT` pour mettre à jour un produit, avec signature HMAC et vérification de la réponse

---

### └── `src/Service/`

- `SignatureService.php` — génère les en-têtes signés pour l’authentification et vérifie la signature des réponses de l’API.

---


