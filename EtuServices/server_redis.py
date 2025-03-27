import redis
import sys
import bcrypt

sys.stdout.reconfigure(encoding='utf-8')

# Connexion à Redis
r = redis.Redis(host='localhost', port=6379, db=0, decode_responses=True)
r_services = redis.Redis(host='localhost', port=6379, db=1, decode_responses=True)

def verify_user(email, password):
    user_key = f"user:{email}"
    user_data = r.hgetall(user_key)

    if not user_data:
        return False, "Utilisateur non trouvé."

    stored_password = user_data.get("password", "")

    try:
        # Vérifie si le mot de passe stocké est bien un hash bcrypt
        if bcrypt.checkpw(password.encode('utf-8'), stored_password.encode('utf-8')):
            return True, "User verified successfully."
        else:
            return False, "Mot de passe incorrect."
    except ValueError:
        return False, "Erreur de vérification du mot de passe."

def check_login_attempts(email):
    attempts_key = f"login_attempts:{email}"
    attempts = r.get(attempts_key)

    if attempts is None:
        r.setex(attempts_key, 600, 1)  # Expire après 10 minutes
        return True, "Connexion autorisée."

    attempts = int(attempts)

    if attempts >= 10:
        return False, "Trop de tentatives. Réessayez plus tard."

    r.incr(attempts_key)  # Incrémente les tentatives
    return True, "Connexion autorisée."

def log_login(email):
    """Ajoute l'utilisateur à la liste des 10 derniers connectés et met à jour le top des connexions."""
    r.lpush("last_logins", email)  
    r.ltrim("last_logins", 0, 9)  # Garde les 10 derniers

    r.zincrby("user_connections", 1, email)  # Incrémente les connexions de l'utilisateur

def log_service_usage(email, service):
    """Enregistre l'utilisation d'un service par un utilisateur dans Redis (DB 1)."""
    service_key = f"services:{service}"
    user_key = f"user_services:{email}"

    r_services.incr(service_key)   # Incrémente le total d'utilisation du service
    r_services.zincrby("user_services", 1, email)  # Incrémente l'utilisation par utilisateur

def log_user_connection(email):
    """Incrémente le nombre de connexions d'un utilisateur"""
    r.zincrby("user_connections", 1, email)

def get_top_3_users():
    return r.zrevrange("user_connections", 0, 2, withscores=True)

def get_last_10_logins():
    return r.lrange("last_logins", 0, 9)

def get_least_used_users():
    return r_services.zrange("user_services", 0, -1, withscores=True)

def get_most_used_service():
    return r_services.zrevrange("services", 0, 0, withscores=True)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python server_redis.py <email> <password> [service]")
        sys.exit(1)

    email = sys.argv[1]
    password = sys.argv[2]

    # Vérification de l'utilisateur
    user_valid, message = verify_user(email, password)
    if not user_valid:
        print(message)
        sys.exit(1)

    # Vérification du nombre de tentatives de connexion
    allowed, login_message = check_login_attempts(email)
    if not allowed:
        print(login_message)
        sys.exit(1)

    # Enregistrer la connexion
    log_login(email)
    print("Connexion réussie.")

    # Si un service est utilisé, on l'enregistre
    if len(sys.argv) == 4:
        service = sys.argv[3]
        log_service_usage(email, service)
        print(f"Service {service} utilisé.")

    # Affichage des statistiques demandées :
    print("\n--- Statistiques ---")
    
    # 1. Les 10 derniers utilisateurs connectés
    last_10_logins = get_last_10_logins()
    print("10 derniers utilisateurs connectés :")
    print(last_10_logins)

    # 2. Le top 3 des utilisateurs les plus connectés
    top_users = get_top_3_users()
    print("\nTop 3 des utilisateurs les plus connectés :")
    for user, score in top_users:
        print(f"{user}: {score} connexions")

    # 3. Les utilisateurs ayant le moins utilisé les services
    least_used_users = get_least_used_users()
    print("\nUtilisateurs ayant le moins utilisé les services :")
    for user, score in least_used_users:
        print(f"{user}: {score} utilisations")

    # 4. Le service le plus utilisé
    most_used_service = get_most_used_service()
    print("\nService le plus utilisé :")
    if most_used_service:
        service, score = most_used_service[0]
        print(f"Service {service}: {score} utilisations")
    else:
        print("Aucun service utilisé.")
