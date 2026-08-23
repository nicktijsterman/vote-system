#!/usr/bin/env bash
set -e

# Non-secret config is supplied via environment: in docker-compose.yml (or -e
# flags for a manual `docker run`), with no file required to pre-exist on the
# host. The handful of secrets that must be generated once and then persist
# across container recreation - APP_KEY, VS_ADMIN_PASSWORD, PUSHER_APP_SECRET -
# live on a named volume shared by the web and websockets containers instead,
# since a bind-mounted .env that doesn't already exist on the host causes
# Docker to silently mount an empty directory in its place, breaking startup.
#
# If a real .env file is separately bind-mounted at /app/.env (the old manual
# `docker run -v .env:/app/.env` pattern still works), Laravel's own dotenv
# loading picks it up as usual; real process environment variables (including
# the ones exported below) take precedence over it for the same key.
SECRETS_DIR=/app/storage/secrets
SECRETS_FILE="${SECRETS_DIR}/generated.env"
LOCK_FILE="${SECRETS_DIR}/.lock"

mkdir -p "$SECRETS_DIR"

random_value() {
    head -c 64 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c "${1:-32}"
}

set_secret() {
    local key="$1" value="$2"
    touch "$SECRETS_FILE"
    if grep -q "^${key}=" "$SECRETS_FILE" 2>/dev/null; then
        sed -i "s|^${key}=.*|${key}=${value}|" "$SECRETS_FILE"
    else
        echo "${key}=${value}" >> "$SECRETS_FILE"
    fi
}

# Both sibling containers can reach this on first boot at once - serialize.
(
    flock -x 200

    if [ -f "$SECRETS_FILE" ]; then
        source "$SECRETS_FILE"
    fi

    if [ -z "$APP_KEY" ]; then
        set_secret APP_KEY "base64:$(head -c 32 /dev/urandom | base64)"
    fi

    if [ -z "$VS_ADMIN_PASSWORD" ]; then
        generated_password="$(random_value 20)"
        set_secret VS_ADMIN_PASSWORD "$generated_password"
        echo "$generated_password" > "${SECRETS_DIR}/.admin-password-just-generated"
    fi

    if [ -z "$PUSHER_APP_SECRET" ]; then
        set_secret PUSHER_APP_SECRET "$(random_value 32)"
    fi
) 200>"$LOCK_FILE"

source "$SECRETS_FILE"
export APP_KEY VS_ADMIN_PASSWORD PUSHER_APP_SECRET

if [ "$1" == "artisan" ]; then
    exec docker-php-entrypoint php "$@"
fi

if [ "$1" != "apache2-foreground" ]; then
    exec docker-php-entrypoint "$@"
fi

# Only the primary (apache) container reaches here, mirroring the previous
# behavior where the websockets container's command starts with "artisan".
banner_lines=()
if [ -f "${SECRETS_DIR}/.admin-password-just-generated" ]; then
    banner_lines+=("Generated admin password: $(cat "${SECRETS_DIR}/.admin-password-just-generated")")
    rm -f "${SECRETS_DIR}/.admin-password-just-generated"
fi

if [ "${#banner_lines[@]}" -gt 0 ]; then
    echo "=============================================================="
    echo " Vote System - generated first-boot credentials"
    for line in "${banner_lines[@]}"; do
        echo " ${line}"
    done
    echo " Saved on the 'secrets' volume; will persist across restarts."
    echo "=============================================================="
fi

# -p preserves the exported secrets above across su, which otherwise isn't
# guaranteed depending on the distro's su/PAM defaults.
su -p www-data -s /bin/bash -c '
php artisan config:cache
php artisan migrate --force --seed
php artisan votesystem:admin
'

exec docker-php-entrypoint "$@"
