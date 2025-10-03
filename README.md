# projet-administrationbdd-001

🗂️ Projet : Système de gestion des droits et traçabilité
🎯 Objectif

Mettre en place une application back-end (sans front complexe, juste API ou CLI/SQL scripts) qui :

👥 Gère des utilisateurs, rôles et permissions (modèle RBAC).

💾 Utilise SQL pour stocker la structure rigide (users/roles/permissions).

📝 Utilise MongoDB pour stocker les logs d’activité et d’audit.

🛠️ Propose une archi clean et modulaire.

📋 Cahier des charges
🗄️ Partie SQL (PostgreSQL ou MySQL)

Tables principales :

users : id, username, password_hash, email, created_at

roles : id, name, description

permissions : id, name, description

role_user : pivot (user_id, role_id)

permission_role : pivot (role_id, permission_id)

Fonctionnalités :

➕ Créer un utilisateur et lui affecter un rôle.

➕ Créer un rôle et y associer des permissions.

🔍 Vérifier si un utilisateur a un droit donné (requête SQL avec jointures).

📜 Partie MongoDB (logs / traçabilité)

Collection activity_logs :

{
  "user_id": 12,
  "action": "CREATE_USER",
  "target": "user:25",
  "timestamp": "2025-10-02T08:00:00Z",
  "ip": "192.168.1.15",
  "status": "success"
}


Fonctionnalités :

📝 À chaque action critique (ex : création/modif d’un rôle, suppression utilisateur), on insère un log.

🔎 Possibilité de filtrer les logs par user, date, type d’action.

🏆 Livrables attendus (pour les étudiants)

📊 Schéma relationnel clair (diagramme SQL).

💻 Script SQL de création + peupler la base (roles/permissions prédéfinis).

🗃️ Script Mongo avec exemples de logs et de requêtes d’analyse.

🎬 Démo d’un use case complet :

👤 Créer un utilisateur → lui affecter un rôle → ⚙️ exécuter une action → 📝 log inséré dans Mongo.

📖 Documenter l’architecture (README).

✅ Compétences travaillées :

🗄️ SQL relationnel (modèle RBAC).

📝 MongoDB (données non structurées, logs, recherche rapide).

🔒 Admin BDD (sécurité, droits, audits).

🛠️ Architecture modulaire (séparation persistance / services).
