ARG BASE_IMAGE=nginx:alpine
FROM ${BASE_IMAGE}
RUN apk add --no-cache curl

# Copy custom nginx.conf with rate limiting configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Nginx already runs as non-root in alpine by default (nginx user)
# Verify and ensure proper ownership
RUN chown -R nginx:nginx /var/cache/nginx /var/log/nginx

USER nginx
