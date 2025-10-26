ARG BASE_IMAGE=nginx:alpine
FROM ${BASE_IMAGE}
RUN apk add --no-cache curl

# Copy custom nginx.conf with rate limiting configuration
COPY nginx.conf /etc/nginx/nginx.conf
