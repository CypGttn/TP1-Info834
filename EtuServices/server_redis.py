import redis
import sys
import bcrypt

sys.stdout.reconfigure(encoding='utf-8')

# Connexion à Redis
r = redis.Redis(host='localhost', port=6379, db=0, decode_responses=True)

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

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python server_redis.py <email> <password>")
        sys.exit(1)

    email = sys.argv[1]
    password = sys.argv[2]

    # Vérification du nombre de tentatives AVANT le mot de passe
    allowed, login_message = check_login_attempts(email)
    if not allowed:
        print(login_message)
        sys.exit(1)

    # Vérification de l'utilisateur
    user_valid, message = verify_user(email, password)
    if not user_valid:
        print(message)
        sys.exit(1)

    # Réinitialisation des tentatives après connexion réussie
    r.delete(f"login_attempts:{email}")

    print("Connexion réussie")  # Correspond exactement à la vérification dans login.php
