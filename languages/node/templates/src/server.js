const { createApp } = require('./app');
const { env } = require('./config/env');
const { logger } = require('./utils/logger');

const app = createApp();

const server = app.listen(env.port, () => {
  logger.info(`${env.appName} server is running on port ${env.port}`);
});

function shutdown(signal) {
  logger.info(`${signal} received. Closing server...`);

  server.close(() => {
    logger.info('Server closed successfully.');
    process.exit(0);
  });
}

process.on('SIGINT', shutdown);
process.on('SIGTERM', shutdown);

