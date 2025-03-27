import redis

# Connect to Redis
r = redis.Redis(host='localhost', port=6379, db=0, decode_responses=True)

def store_user(email, password):
    user_key = f"user:{email}"
    user_data = {
        "email": email,
        "password": password
    }
    # Use hset instead of hmset
    r.hset(user_key, mapping=user_data)
    print(f"User {email} stored successfully.")

if __name__ == "__main__":
    # Example usage
    email = "example@example.com"
    password = "securepassword"

    store_user(email, password)
