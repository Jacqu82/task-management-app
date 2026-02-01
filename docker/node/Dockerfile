ARG NODE_VERSION=22
FROM node:${NODE_VERSION}

WORKDIR /web/frontend

# Copy entrypoint
COPY node/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Default entrypoint
ENTRYPOINT ["/entrypoint.sh"]